<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $tenants = DB::table('tenants')->select('id', 'name', 'short_name')->orderBy('name')->get();

        // Quick platform summary for the report page header
        $summary = DB::selectOne("
            SELECT
                (SELECT COALESCE(SUM(available_balance),0) FROM accounts WHERE status='active') AS total_deposits,
                (SELECT COALESCE(SUM(outstanding_balance),0) FROM loans WHERE status IN ('active','overdue')) AS total_loans,
                (SELECT COALESCE(SUM(outstanding_balance),0) FROM loans WHERE status IN ('overdue','defaulted')) AS npl_balance,
                (SELECT COALESCE(SUM(outstanding_balance),0) FROM loans WHERE status IN ('active','overdue','defaulted')) AS total_portfolio,
                (SELECT COUNT(*) FROM customers WHERE status='active') AS active_customers,
                (SELECT COUNT(*) FROM transactions WHERE created_at >= DATE_TRUNC('month', NOW())) AS txns_this_month,
                (SELECT COALESCE(SUM(amount),0) FROM transactions WHERE created_at >= DATE_TRUNC('month', NOW())) AS volume_this_month
        ");

        return view('reports.index', compact('tenants', 'summary'));
    }

    public function depositsReport(Request $request)
    {
        $request->validate([
            'from'      => 'required|date',
            'to'        => 'required|date|after_or_equal:from',
            'tenant_id' => 'nullable|uuid',
        ]);

        $rows = DB::select("
            SELECT
                t.name AS tenant_name,
                t.short_name AS tenant_code,
                TO_CHAR(DATE_TRUNC('month', tx.created_at), 'Mon YYYY') AS month,
                DATE_TRUNC('month', tx.created_at) AS month_sort,
                COUNT(*) AS txn_count,
                COALESCE(SUM(tx.amount), 0) AS total_amount
            FROM transactions tx
            LEFT JOIN tenants t ON t.id = tx.tenant_id
            WHERE tx.type = 'deposit'
              AND tx.created_at BETWEEN :from AND :to
              " . ($request->filled('tenant_id') ? "AND tx.tenant_id = :tenant_id" : "") . "
            GROUP BY t.name, t.short_name, DATE_TRUNC('month', tx.created_at)
            ORDER BY t.name, month_sort
        ", array_filter([
            'from'      => $request->from . ' 00:00:00',
            'to'        => $request->to . ' 23:59:59',
            'tenant_id' => $request->tenant_id ?: null,
        ]));

        if ($request->boolean('export')) {
            return $this->exportCsv('deposits_report_' . $request->from . '_' . $request->to,
                ['Tenant', 'Code', 'Month', 'Transactions', 'Total Amount (₦)'],
                array_map(fn($r) => [$r->tenant_name, $r->tenant_code, $r->month, $r->txn_count, number_format($r->total_amount, 2)], $rows)
            );
        }

        return view('reports.deposits', compact('rows', 'request'));
    }

    public function loanPortfolioReport(Request $request)
    {
        $request->validate([
            'tenant_id' => 'nullable|uuid',
        ]);

        $rows = DB::select("
            SELECT
                t.name AS tenant_name,
                t.short_name AS tenant_code,
                COUNT(*) AS total_loans,
                SUM(CASE WHEN l.status = 'active' THEN 1 ELSE 0 END) AS active_loans,
                SUM(CASE WHEN l.status = 'overdue' THEN 1 ELSE 0 END) AS overdue_loans,
                SUM(CASE WHEN l.status = 'defaulted' THEN 1 ELSE 0 END) AS defaulted_loans,
                SUM(CASE WHEN l.status = 'completed' THEN 1 ELSE 0 END) AS completed_loans,
                COALESCE(SUM(l.principal_amount), 0) AS total_disbursed,
                COALESCE(SUM(l.outstanding_balance), 0) AS total_outstanding,
                COALESCE(SUM(CASE WHEN l.status IN ('overdue','defaulted') THEN l.outstanding_balance ELSE 0 END), 0) AS par_balance,
                COALESCE(SUM(CASE WHEN l.status = 'defaulted' THEN l.outstanding_balance ELSE 0 END), 0) AS npl_balance
            FROM loans l
            LEFT JOIN tenants t ON t.id = l.tenant_id
            " . ($request->filled('tenant_id') ? "WHERE l.tenant_id = :tenant_id" : "") . "
            GROUP BY t.name, t.short_name
            ORDER BY total_outstanding DESC
        ", $request->filled('tenant_id') ? ['tenant_id' => $request->tenant_id] : []);

        foreach ($rows as &$row) {
            $row->par_ratio = $row->total_outstanding > 0 ? round(($row->par_balance / $row->total_outstanding) * 100, 2) : 0;
            $row->npl_ratio = $row->total_outstanding > 0 ? round(($row->npl_balance / $row->total_outstanding) * 100, 2) : 0;
        }

        if ($request->boolean('export')) {
            return $this->exportCsv('loan_portfolio_' . now()->format('Y-m-d'),
                ['Tenant', 'Code', 'Total Loans', 'Active', 'Overdue', 'Defaulted', 'Completed', 'Disbursed (₦)', 'Outstanding (₦)', 'PAR %', 'NPL %'],
                array_map(fn($r) => [$r->tenant_name, $r->tenant_code, $r->total_loans, $r->active_loans, $r->overdue_loans, $r->defaulted_loans, $r->completed_loans, number_format($r->total_disbursed,2), number_format($r->total_outstanding,2), $r->par_ratio.'%', $r->npl_ratio.'%'], $rows)
            );
        }

        return view('reports.loan-portfolio', compact('rows', 'request'));
    }

    public function customerGrowthReport(Request $request)
    {
        $request->validate([
            'from'      => 'nullable|date',
            'to'        => 'nullable|date',
            'tenant_id' => 'nullable|uuid',
        ]);

        $from = $request->from ?? now()->subMonths(6)->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $rows = DB::select("
            SELECT
                t.name AS tenant_name,
                t.short_name AS tenant_code,
                TO_CHAR(DATE_TRUNC('month', c.created_at), 'Mon YYYY') AS month,
                DATE_TRUNC('month', c.created_at) AS month_sort,
                COUNT(*) AS new_customers,
                SUM(CASE WHEN c.kyc_status = 'approved' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN c.kyc_status = 'manual_review' THEN 1 ELSE 0 END) AS pending_kyc
            FROM customers c
            LEFT JOIN tenants t ON t.id = c.tenant_id
            WHERE c.created_at BETWEEN :from AND :to
            " . ($request->filled('tenant_id') ? "AND c.tenant_id = :tenant_id" : "") . "
            GROUP BY t.name, t.short_name, DATE_TRUNC('month', c.created_at)
            ORDER BY t.name, month_sort
        ", array_filter([
            'from'      => $from . ' 00:00:00',
            'to'        => $to . ' 23:59:59',
            'tenant_id' => $request->tenant_id ?: null,
        ]));

        if ($request->boolean('export')) {
            return $this->exportCsv('customer_growth_' . $from . '_' . $to,
                ['Tenant', 'Code', 'Month', 'New Customers', 'KYC Approved', 'Pending KYC'],
                array_map(fn($r) => [$r->tenant_name, $r->tenant_code, $r->month, $r->new_customers, $r->approved, $r->pending_kyc], $rows)
            );
        }

        return view('reports.customer-growth', compact('rows', 'from', 'to', 'request'));
    }

    public function transactionSummaryReport(Request $request)
    {
        $request->validate([
            'from'      => 'nullable|date',
            'to'        => 'nullable|date',
            'tenant_id' => 'nullable|uuid',
        ]);

        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $rows = DB::select("
            SELECT
                t.name AS tenant_name,
                t.short_name AS tenant_code,
                tx.type,
                COUNT(*) AS txn_count,
                COALESCE(SUM(tx.amount), 0) AS total_amount,
                COALESCE(AVG(tx.amount), 0) AS avg_amount,
                COALESCE(MAX(tx.amount), 0) AS max_amount
            FROM transactions tx
            LEFT JOIN tenants t ON t.id = tx.tenant_id
            WHERE tx.created_at BETWEEN :from AND :to
            " . ($request->filled('tenant_id') ? "AND tx.tenant_id = :tenant_id" : "") . "
            GROUP BY t.name, t.short_name, tx.type
            ORDER BY t.name, total_amount DESC
        ", array_filter([
            'from'      => $from . ' 00:00:00',
            'to'        => $to . ' 23:59:59',
            'tenant_id' => $request->tenant_id ?: null,
        ]));

        if ($request->boolean('export')) {
            return $this->exportCsv('transaction_summary_' . $from . '_' . $to,
                ['Tenant', 'Code', 'Type', 'Count', 'Total (₦)', 'Avg (₦)', 'Max (₦)'],
                array_map(fn($r) => [$r->tenant_name, $r->tenant_code, $r->type, $r->txn_count, number_format($r->total_amount,2), number_format($r->avg_amount,2), number_format($r->max_amount,2)], $rows)
            );
        }

        return view('reports.transaction-summary', compact('rows', 'from', 'to', 'request'));
    }

    private function exportCsv(string $filename, array $headers, array $rows): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename . '.csv', ['Content-Type' => 'text/csv']);
    }
}
