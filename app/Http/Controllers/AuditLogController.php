<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $tenants = DB::table('tenants')->select('id', 'name')->orderBy('name')->get();

        // Combined query: audit_logs joined with users and tenants
        $query = DB::table('audit_logs as al')
            ->leftJoin('tenants as t', 't.id', '=', 'al.tenant_id')
            ->leftJoin('users as u', DB::raw('u.id::text'), '=', DB::raw('al.user_id::text'))
            ->select(
                'al.id',
                'al.action',
                'al.entity_type',
                'al.entity_id',
                'al.description',
                'al.ip_address',
                'al.created_at',
                't.name AS tenant_name',
                't.short_name AS tenant_code',
                DB::raw("COALESCE(u.name, 'System') AS user_name")
            );

        if ($request->filled('tenant_id')) {
            $query->where('al.tenant_id', $request->tenant_id);
        }
        if ($request->filled('action')) {
            $query->where('al.action', $request->action);
        }
        if ($request->filled('entity_type')) {
            $query->where('al.entity_type', 'ILIKE', '%' . $request->entity_type . '%');
        }
        if ($request->filled('from')) {
            $query->whereDate('al.created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('al.created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $like = '%' . $request->search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('al.description', 'ILIKE', $like)
                  ->orWhere('al.entity_id', 'ILIKE', $like)
                  ->orWhere('al.entity_type', 'ILIKE', $like);
            });
        }

        $logs = $query->orderByDesc('al.created_at')->paginate(30)->withQueryString();

        // Summary stats
        $stats = DB::table('audit_logs')
            ->selectRaw("
                COUNT(*) AS total,
                COUNT(DISTINCT tenant_id) AS tenants_active,
                SUM(CASE WHEN action = 'login' THEN 1 ELSE 0 END) AS logins,
                SUM(CASE WHEN created_at >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) AS last_24h
            ")
            ->first();

        $actionCounts = DB::table('audit_logs')
            ->selectRaw('action, COUNT(*) AS cnt')
            ->where('created_at', '>=', DB::raw("NOW() - INTERVAL '30 days'"))
            ->groupBy('action')
            ->orderByDesc('cnt')
            ->get();

        return view('audit-log', compact('logs', 'tenants', 'stats', 'actionCounts'));
    }
}
