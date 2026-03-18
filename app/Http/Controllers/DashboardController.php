<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('superadmin')->user();

        // Platform-wide KPIs
        $totalTenants     = DB::table('tenants')->where('status', 'active')->count();
        $totalCustomers   = DB::table('customers')->count();
        $totalAccounts    = DB::table('accounts')->where('status', 'active')->count();
        $totalDeposits    = DB::table('accounts')->whereIn('type', ['savings','current','fixed'])->sum('available_balance');
        $totalLoanBook    = DB::table('loans')->whereIn('status', ['active','overdue','defaulted'])->sum('outstanding_balance');
        $todayTxnCount    = DB::table('transactions')->whereDate('created_at', today())->count();
        $todayTxnVolume   = DB::table('transactions')->whereDate('created_at', today())->sum('amount');
        $pendingKyc       = DB::table('kyc_upgrade_requests')->where('status','submitted')->count();
        $pendingLoans     = DB::table('loan_applications')->where('status','submitted')->count();

        // Per-tenant stats
        $tenants = DB::table('tenants')
            ->leftJoin('customers', 'tenants.id', '=', 'customers.tenant_id')
            ->leftJoin('accounts', 'tenants.id', '=', 'accounts.tenant_id')
            ->select(
                'tenants.id',
                'tenants.name',
                'tenants.short_name',
                'tenants.status',
                'tenants.subscription_plan',
                DB::raw('COUNT(DISTINCT customers.id) as customer_count'),
                DB::raw("COALESCE(SUM(CASE WHEN accounts.type IN ('savings','current','fixed') THEN accounts.available_balance END),0) as deposit_book")
            )
            ->groupBy('tenants.id','tenants.name','tenants.short_name','tenants.status','tenants.subscription_plan')
            ->orderByDesc('deposit_book')
            ->get();

        // Recent transactions across all tenants
        $recentTransactions = DB::table('transactions')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->join('customers', 'accounts.customer_id', '=', 'customers.id')
            ->select(
                'transactions.id',
                'transactions.reference',
                'transactions.type',
                'transactions.amount',
                'transactions.status',
                'transactions.created_at',
                DB::raw("customers.first_name || ' ' || customers.last_name as customer_name"),
                'accounts.account_number'
            )
            ->orderByDesc('transactions.created_at')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'admin',
            'totalTenants', 'totalCustomers', 'totalAccounts',
            'totalDeposits', 'totalLoanBook',
            'todayTxnCount', 'todayTxnVolume',
            'pendingKyc', 'pendingLoans',
            'tenants', 'recentTransactions'
        ));
    }
}
