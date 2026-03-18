<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    // -------------------------------------------------------------------------
    // INDEX — List all tenants with aggregated stats
    // -------------------------------------------------------------------------
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $statusFilter = $request->input('status', 'all');

        $query = DB::table('tenants')
            ->leftJoin(
                DB::raw('(SELECT tenant_id, COUNT(*) AS customer_count FROM customers GROUP BY tenant_id) AS cust_agg'),
                'tenants.id', '=', 'cust_agg.tenant_id'
            )
            ->leftJoin(
                DB::raw('(SELECT tenant_id, COUNT(*) AS account_count, COALESCE(SUM(available_balance),0) AS deposit_book FROM accounts GROUP BY tenant_id) AS acc_agg'),
                'tenants.id', '=', 'acc_agg.tenant_id'
            )
            ->leftJoin(
                DB::raw("(SELECT tenant_id, COALESCE(SUM(outstanding_balance),0) AS loan_book FROM loans WHERE status IN ('active','overdue','defaulted') GROUP BY tenant_id) AS loan_agg"),
                'tenants.id', '=', 'loan_agg.tenant_id'
            )
            ->leftJoin(
                DB::raw('(SELECT tenant_id, COUNT(*) AS user_count FROM users GROUP BY tenant_id) AS usr_agg'),
                'tenants.id', '=', 'usr_agg.tenant_id'
            )
            ->select(
                'tenants.id',
                'tenants.name',
                'tenants.short_name',
                'tenants.type',
                'tenants.status',
                'tenants.subscription_plan',
                'tenants.domain',
                'tenants.created_at',
                DB::raw('COALESCE(cust_agg.customer_count, 0) AS customer_count'),
                DB::raw('COALESCE(acc_agg.account_count, 0) AS account_count'),
                DB::raw('COALESCE(acc_agg.deposit_book, 0) AS deposit_book'),
                DB::raw('COALESCE(loan_agg.loan_book, 0) AS loan_book'),
                DB::raw('COALESCE(usr_agg.user_count, 0) AS user_count')
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tenants.name', 'ILIKE', "%{$search}%")
                  ->orWhere('tenants.short_name', 'ILIKE', "%{$search}%");
            });
        }

        if ($statusFilter !== 'all') {
            $query->where('tenants.status', $statusFilter);
        }

        $tenants = $query->orderBy('tenants.created_at', 'desc')->get();

        // Summary stats
        $totalTenants   = DB::table('tenants')->count();
        $activeTenants  = DB::table('tenants')->where('status', 'active')->count();
        $suspendedTenants = DB::table('tenants')->where('status', 'suspended')->count();
        $totalCustomers = DB::table('customers')->count();

        return view('tenants.index', compact(
            'tenants', 'search', 'statusFilter',
            'totalTenants', 'activeTenants', 'suspendedTenants', 'totalCustomers'
        ));
    }

    // -------------------------------------------------------------------------
    // SHOW — Tenant detail
    // -------------------------------------------------------------------------
    public function show($id)
    {
        $tenant = DB::table('tenants')->where('id', $id)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        // KPI: customers
        $customerCount = DB::table('customers')->where('tenant_id', $id)->count();

        // KPI: active accounts
        $activeAccountCount = DB::table('accounts')
            ->where('tenant_id', $id)
            ->where('status', 'active')
            ->count();

        // KPI: deposit book
        $depositBook = DB::table('accounts')
            ->where('tenant_id', $id)
            ->sum('available_balance') ?? 0;

        // KPI: loan book (active/overdue/defaulted)
        $loanBook = DB::table('loans')
            ->where('tenant_id', $id)
            ->whereIn('status', ['active', 'overdue', 'defaulted'])
            ->sum('outstanding_balance') ?? 0;

        // KPI: active loans count
        $activeLoanCount = DB::table('loans')
            ->where('tenant_id', $id)
            ->whereIn('status', ['active', 'overdue', 'defaulted'])
            ->count();

        // KPI: today's transactions count
        $todayTxCount = DB::table('transactions')
            ->where('tenant_id', $id)
            ->whereRaw("DATE(created_at) = CURRENT_DATE")
            ->count();

        // Recent 10 customers
        $recentCustomers = DB::table('customers')
            ->where('tenant_id', $id)
            ->select('id', 'first_name', 'last_name', 'phone', 'kyc_tier', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent 10 transactions
        $recentTransactions = DB::table('transactions')
            ->where('tenant_id', $id)
            ->select('id', 'reference', 'type', 'amount', 'currency', 'description', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Admin users for this tenant
        $adminUsers = DB::table('users')
            ->where('tenant_id', $id)
            ->select('id', 'name', 'email', 'status', 'last_login_at', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('tenants.show', compact(
            'tenant',
            'customerCount', 'activeAccountCount', 'depositBook', 'loanBook',
            'activeLoanCount', 'todayTxCount',
            'recentCustomers', 'recentTransactions', 'adminUsers'
        ));
    }

    // -------------------------------------------------------------------------
    // CREATE — Show form
    // -------------------------------------------------------------------------
    public function create()
    {
        // Generate a suggested password
        $suggestedPassword = Str::random(10);
        return view('tenants.create', compact('suggestedPassword'));
    }

    // -------------------------------------------------------------------------
    // STORE — Create new tenant + first admin user
    // -------------------------------------------------------------------------
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'short_name'             => 'required|string|max:20|regex:/^[a-zA-Z0-9_-]+$/|unique:tenants,short_name',
            'type'                   => 'required|in:bank,cooperative,digital_lender,microfinance',
            'subscription_plan'      => 'required|in:starter,growth,enterprise',
            'domain'                 => 'nullable|string|max:255',
            'contact_email'          => 'nullable|email|max:255',
            'contact_phone'          => 'nullable|string|max:50',
            'cbn_license_number'     => 'nullable|string|max:100',
            'nibss_institution_code' => 'nullable|string|max:20',
            'address'                => 'nullable|string|max:500',
            'primary_color'          => 'nullable|string|max:7',
            'secondary_color'        => 'nullable|string|max:7',
            'admin_name'             => 'required|string|max:255',
            'admin_email'            => 'required|email|max:255|unique:users,email',
            'admin_password'         => 'required|string|min:8',
        ]);

        $tenantId = (string) Str::uuid();

        DB::table('tenants')->insert([
            'id'                     => $tenantId,
            'name'                   => $validated['name'],
            'short_name'             => strtolower($validated['short_name']),
            'type'                   => $validated['type'],
            'subscription_plan'      => $validated['subscription_plan'],
            'domain'                 => $validated['domain'] ?? null,
            'contact_email'          => $validated['contact_email'] ?? null,
            'contact_phone'          => $validated['contact_phone'] ?? null,
            'cbn_license_number'     => $validated['cbn_license_number'] ?? null,
            'nibss_institution_code' => $validated['nibss_institution_code'] ?? null,
            'address'                => $validated['address'] ? json_encode(['line1' => $validated['address']]) : null,
            'primary_color'          => $validated['primary_color'] ?? '#2563eb',
            'secondary_color'        => $validated['secondary_color'] ?? '#0c2461',
            'primary_currency'       => 'NGN',
            'supported_currencies'   => json_encode(['NGN']),
            'status'                 => 'active',
            'onboarding_step'        => 0,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // Create first admin user
        DB::table('users')->insert([
            'name'                => $validated['admin_name'],
            'email'               => $validated['admin_email'],
            'password'            => Hash::make($validated['admin_password']),
            'tenant_id'           => $tenantId,
            'status'              => 'active',
            'must_change_password' => true,
            'failed_login_count'  => 0,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        return redirect()->route('tenants.show', $tenantId)
            ->with('success', "Tenant '{$validated['name']}' created successfully.")
            ->with('admin_credentials', [
                'name'      => $validated['admin_name'],
                'email'     => $validated['admin_email'],
                'password'  => $validated['admin_password'],
                'short_name' => strtolower($validated['short_name']),
            ]);
    }

    // -------------------------------------------------------------------------
    // EDIT — Show edit form
    // -------------------------------------------------------------------------
    public function edit($id)
    {
        $tenant = DB::table('tenants')->where('id', $id)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        return view('tenants.edit', compact('tenant'));
    }

    // -------------------------------------------------------------------------
    // UPDATE — Update tenant settings
    // -------------------------------------------------------------------------
    public function update(Request $request, $id)
    {
        $tenant = DB::table('tenants')->where('id', $id)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'short_name'             => 'required|string|max:20|regex:/^[a-zA-Z0-9_-]+$/|unique:tenants,short_name,' . $id . ',id',
            'type'                   => 'required|in:bank,cooperative,digital_lender,microfinance',
            'subscription_plan'      => 'required|in:starter,growth,enterprise',
            'domain'                 => 'nullable|string|max:255',
            'contact_email'          => 'nullable|email|max:255',
            'contact_phone'          => 'nullable|string|max:50',
            'cbn_license_number'     => 'nullable|string|max:100',
            'nibss_institution_code' => 'nullable|string|max:20',
            'address'                => 'nullable|string|max:500',
            'primary_color'          => 'nullable|string|max:7',
            'secondary_color'        => 'nullable|string|max:7',
        ]);

        DB::table('tenants')->where('id', $id)->update([
            'name'                   => $validated['name'],
            'short_name'             => strtolower($validated['short_name']),
            'type'                   => $validated['type'],
            'subscription_plan'      => $validated['subscription_plan'],
            'domain'                 => $validated['domain'] ?? null,
            'contact_email'          => $validated['contact_email'] ?? null,
            'contact_phone'          => $validated['contact_phone'] ?? null,
            'cbn_license_number'     => $validated['cbn_license_number'] ?? null,
            'nibss_institution_code' => $validated['nibss_institution_code'] ?? null,
            'address'                => $validated['address'] ? json_encode(['line1' => $validated['address']]) : null,
            'primary_color'          => $validated['primary_color'] ?? '#2563eb',
            'secondary_color'        => $validated['secondary_color'] ?? '#0c2461',
            'updated_at'             => now(),
        ]);

        return redirect()->route('tenants.show', $id)
            ->with('success', "Tenant '{$validated['name']}' updated successfully.");
    }

    // -------------------------------------------------------------------------
    // SUSPEND
    // -------------------------------------------------------------------------
    public function suspend(Request $request, $id)
    {
        $tenant = DB::table('tenants')->where('id', $id)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        $request->validate([
            'suspension_reason' => 'required|string|max:1000',
        ]);

        DB::table('tenants')->where('id', $id)->update([
            'status'             => 'suspended',
            'suspension_reason'  => $request->input('suspension_reason'),
            'suspended_at'       => now(),
            'updated_at'         => now(),
        ]);

        return redirect()->route('tenants.show', $id)
            ->with('success', "Tenant '{$tenant->name}' has been suspended.");
    }

    // -------------------------------------------------------------------------
    // ACTIVATE
    // -------------------------------------------------------------------------
    public function activate($id)
    {
        $tenant = DB::table('tenants')->where('id', $id)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        DB::table('tenants')->where('id', $id)->update([
            'status'             => 'active',
            'suspension_reason'  => null,
            'suspended_at'       => null,
            'updated_at'         => now(),
        ]);

        return redirect()->route('tenants.show', $id)
            ->with('success', "Tenant '{$tenant->name}' has been reactivated.");
    }
}
