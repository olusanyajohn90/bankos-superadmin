<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Monthly customer growth — last 12 months
        $monthlyGrowth = DB::select("
            SELECT TO_CHAR(created_at, 'YYYY-MM') AS month,
                   COUNT(*) AS new_customers
            FROM customers
            WHERE created_at >= NOW() - INTERVAL '12 months'
            GROUP BY month
            ORDER BY month ASC
        ");

        // 2. Monthly transaction volume — last 6 months (deposits vs disbursements)
        $monthlyVolume = DB::select("
            SELECT TO_CHAR(created_at, 'YYYY-MM') AS month,
                   SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) AS deposits,
                   SUM(CASE WHEN type = 'disbursement' THEN amount ELSE 0 END) AS disbursements
            FROM transactions
            WHERE created_at >= NOW() - INTERVAL '6 months'
            GROUP BY month
            ORDER BY month ASC
        ");

        // 3. Monthly new loans — last 6 months
        $monthlyLoans = DB::select("
            SELECT TO_CHAR(created_at, 'YYYY-MM') AS month,
                   COUNT(*) AS new_loans,
                   SUM(principal_amount) AS total_amount
            FROM loans
            WHERE created_at >= NOW() - INTERVAL '6 months'
            GROUP BY month
            ORDER BY month ASC
        ");

        // 4. Transaction type breakdown — last 30 days
        $txnTypeBreakdown = DB::select("
            SELECT type,
                   COUNT(*) AS txn_count,
                   SUM(amount) AS total_amount
            FROM transactions
            WHERE created_at >= NOW() - INTERVAL '30 days'
            GROUP BY type
            ORDER BY total_amount DESC
        ");

        // Calculate total volume for percentage
        $totalVolume30d = array_sum(array_map(fn($r) => (float)$r->total_amount, $txnTypeBreakdown));

        // 5. Top 10 customers by balance
        $topCustomers = DB::select("
            SELECT
                c.first_name || ' ' || c.last_name AS full_name,
                c.customer_number,
                c.phone,
                t.name AS tenant_name,
                COUNT(a.id) AS account_count,
                COALESCE(SUM(a.available_balance), 0) AS total_balance
            FROM customers c
            LEFT JOIN accounts a ON a.customer_id = c.id
            LEFT JOIN tenants t ON t.id = c.tenant_id
            GROUP BY c.id, c.first_name, c.last_name, c.customer_number, c.phone, t.name
            ORDER BY total_balance DESC
            LIMIT 10
        ");

        // 6. Account type distribution
        $accountTypeDistribution = DB::select("
            SELECT type,
                   COUNT(*) AS account_count,
                   COALESCE(SUM(available_balance), 0) AS total_balance
            FROM accounts
            GROUP BY type
            ORDER BY account_count DESC
        ");

        // 7. Loan status distribution
        $loanStatusDistribution = DB::select("
            SELECT status,
                   COUNT(*) AS loan_count,
                   COALESCE(SUM(outstanding_balance), 0) AS total_outstanding
            FROM loans
            GROUP BY status
            ORDER BY loan_count DESC
        ");

        // 8. Platform totals
        $platformTotals = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM customers) AS total_customers,
                (SELECT COUNT(*) FROM accounts WHERE status = 'active') AS total_active_accounts,
                (SELECT COALESCE(SUM(available_balance), 0) FROM accounts WHERE status = 'active') AS total_deposits,
                (SELECT COALESCE(SUM(outstanding_balance), 0) FROM loans WHERE status IN ('active', 'overdue')) AS total_loan_book,
                (SELECT COUNT(*) FROM transactions) AS total_transactions,
                (SELECT COALESCE(AVG(available_balance), 0) FROM accounts WHERE status = 'active') AS avg_account_balance
        ");

        return view('analytics.index', compact(
            'monthlyGrowth',
            'monthlyVolume',
            'monthlyLoans',
            'txnTypeBreakdown',
            'totalVolume30d',
            'topCustomers',
            'accountTypeDistribution',
            'loanStatusDistribution',
            'platformTotals'
        ));
    }

    public function data()
    {
        return response()->json([
            'timestamp'          => now()->toISOString(),
            'total_transactions' => DB::table('transactions')->count(),
            'total_customers'    => DB::table('customers')->count(),
            'total_deposits'     => DB::table('accounts')->where('status', 'active')->sum('available_balance'),
        ]);
    }

    public function customers(Request $request)
    {
        $tenants = DB::table('tenants')->select('id', 'name')->orderBy('name')->get();

        $query = DB::table('customers as c')
            ->leftJoin('tenants as t', 't.id', '=', 'c.tenant_id')
            ->leftJoin(
                DB::raw('(SELECT customer_id, COUNT(*) AS account_count, COALESCE(SUM(available_balance),0) AS total_balance FROM accounts GROUP BY customer_id) AS acct'),
                'acct.customer_id',
                '=',
                'c.id'
            )
            ->select(
                'c.id',
                'c.customer_number',
                DB::raw("c.first_name || ' ' || c.last_name AS full_name"),
                'c.phone',
                'c.email',
                'c.kyc_tier',
                'c.status',
                'c.created_at',
                't.name AS tenant_name',
                DB::raw('COALESCE(acct.account_count, 0) AS account_count'),
                DB::raw('COALESCE(acct.total_balance, 0) AS total_balance')
            );

        if ($request->filled('tenant_id')) {
            $query->where('c.tenant_id', $request->tenant_id);
        }
        if ($request->filled('status')) {
            $query->where('c.status', $request->status);
        }
        if ($request->filled('kyc_tier')) {
            $query->where('c.kyc_tier', $request->kyc_tier);
        }
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("c.first_name || ' ' || c.last_name"), 'ILIKE', $search)
                  ->orWhere('c.phone', 'ILIKE', $search)
                  ->orWhere('c.email', 'ILIKE', $search)
                  ->orWhere('c.customer_number', 'ILIKE', $search);
            });
        }

        $totalCount = $query->count();
        $customers  = $query->orderBy('c.created_at', 'desc')->paginate(25)->withQueryString();

        return view('analytics.customers', compact('customers', 'tenants', 'totalCount'));
    }

    public function transactions(Request $request)
    {
        $tenants = DB::table('tenants')->select('id', 'name')->orderBy('name')->get();

        // Build a reusable base filter for the stats query (only on transactions table)
        $statsQuery = DB::table('transactions as tx');
        if ($request->filled('tenant_id')) {
            $statsQuery->where('tx.tenant_id', $request->tenant_id);
        }
        if ($request->filled('type')) {
            $statsQuery->where('tx.type', $request->type);
        }
        if ($request->filled('status')) {
            $statsQuery->where('tx.status', $request->status);
        }
        if ($request->filled('from')) {
            $statsQuery->whereDate('tx.created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $statsQuery->whereDate('tx.created_at', '<=', $request->to);
        }
        // Note: search filter is NOT applied to stats (needs join) — acceptable approximation

        $totalCount  = $statsQuery->count();
        $totalVolume = $statsQuery->sum('tx.amount');

        // Full query with joins for display
        $query = DB::table('transactions as tx')
            ->leftJoin('accounts as a', 'a.id', '=', 'tx.account_id')
            ->leftJoin('customers as c', 'c.id', '=', 'a.customer_id')
            ->leftJoin('tenants as t', 't.id', '=', 'tx.tenant_id')
            ->select(
                'tx.id',
                'tx.reference',
                'tx.type',
                'tx.amount',
                'tx.currency',
                'tx.status',
                'tx.description',
                'tx.created_at',
                'a.account_number',
                DB::raw("COALESCE(c.first_name || ' ' || c.last_name, 'Unknown') AS customer_name"),
                't.name AS tenant_name'
            );

        if ($request->filled('tenant_id')) {
            $query->where('tx.tenant_id', $request->tenant_id);
        }
        if ($request->filled('type')) {
            $query->where('tx.type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('tx.status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('tx.created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tx.created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('tx.reference', 'ILIKE', $search)
                  ->orWhere(DB::raw("c.first_name || ' ' || c.last_name"), 'ILIKE', $search);
            });
        }

        $transactions = $query->orderBy('tx.created_at', 'desc')->paginate(25)->withQueryString();

        return view('analytics.transactions', compact('transactions', 'tenants', 'totalCount', 'totalVolume'));
    }
}
