<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KycController extends Controller
{
    public function index(Request $request)
    {
        $tenants = DB::table('tenants')->select('id', 'name')->orderBy('name')->get();

        // KYC queue: customers needing manual review
        $query = DB::table('customers as c')
            ->leftJoin('tenants as t', 't.id', '=', 'c.tenant_id')
            ->leftJoin(
                DB::raw('(SELECT customer_id, COUNT(*) AS doc_count, MAX(created_at) AS latest_doc FROM kyc_documents GROUP BY customer_id) AS kd'),
                'kd.customer_id', '=', 'c.id'
            )
            ->where('c.kyc_status', 'manual_review')
            ->select(
                'c.id',
                'c.customer_number',
                DB::raw("c.first_name || ' ' || c.last_name AS full_name"),
                'c.phone',
                'c.email',
                'c.kyc_tier',
                'c.kyc_status',
                'c.status',
                'c.created_at',
                't.name AS tenant_name',
                't.id AS tenant_id',
                DB::raw('COALESCE(kd.doc_count, 0) AS doc_count'),
                'kd.latest_doc'
            );

        if ($request->filled('tenant_id')) {
            $query->where('c.tenant_id', $request->tenant_id);
        }
        if ($request->filled('kyc_tier')) {
            $query->where('c.kyc_tier', $request->kyc_tier);
        }
        if ($request->filled('search')) {
            $like = '%' . $request->search . '%';
            $query->where(function ($q) use ($like) {
                $q->where(DB::raw("c.first_name || ' ' || c.last_name"), 'ILIKE', $like)
                  ->orWhere('c.phone', 'ILIKE', $like)
                  ->orWhere('c.customer_number', 'ILIKE', $like);
            });
        }

        $customers = $query->orderBy('kd.latest_doc', 'desc')->paginate(25)->withQueryString();

        // Stats
        $stats = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM customers WHERE kyc_status = 'manual_review') AS pending,
                (SELECT COUNT(*) FROM kyc_documents WHERE status = 'pending') AS pending_docs,
                (SELECT COUNT(*) FROM customers WHERE kyc_status = 'approved' AND updated_at >= NOW() - INTERVAL '7 days') AS approved_7d,
                (SELECT COUNT(*) FROM customers WHERE kyc_status = 'rejected' AND updated_at >= NOW() - INTERVAL '7 days') AS rejected_7d
        ");

        // Breakdown by tenant
        $byTenant = DB::table('customers as c')
            ->leftJoin('tenants as t', 't.id', '=', 'c.tenant_id')
            ->where('c.kyc_status', 'manual_review')
            ->selectRaw('t.name AS tenant_name, COUNT(*) AS cnt')
            ->groupBy('t.name')
            ->orderByDesc('cnt')
            ->get();

        return view('kyc.index', compact('customers', 'tenants', 'stats', 'byTenant'));
    }

    public function show(string $customerId)
    {
        $customer = DB::table('customers as c')
            ->leftJoin('tenants as t', 't.id', '=', 'c.tenant_id')
            ->where('c.id', $customerId)
            ->select(
                'c.*',
                't.name AS tenant_name'
            )
            ->first();

        if (!$customer) abort(404);

        $documents = DB::table('kyc_documents')
            ->where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->get();

        $accounts = DB::table('accounts')
            ->where('customer_id', $customerId)
            ->select('account_number', 'type', 'status', 'available_balance')
            ->get();

        return view('kyc.show', compact('customer', 'documents', 'accounts'));
    }
}
