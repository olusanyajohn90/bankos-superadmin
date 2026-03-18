<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class SystemHealthController extends Controller
{
    public function index()
    {
        $phpVersion     = PHP_VERSION;
        $laravelVersion = app()->version();

        try {
            DB::selectOne('SELECT 1');
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'error';
        }

        $dbSize = DB::selectOne("SELECT pg_size_pretty(pg_database_size(current_database())) AS size");

        $counts = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM tenants)      AS tenants,
                (SELECT COUNT(*) FROM customers)    AS customers,
                (SELECT COUNT(*) FROM accounts)     AS accounts,
                (SELECT COUNT(*) FROM transactions) AS transactions,
                (SELECT COUNT(*) FROM loans)        AS loans,
                (SELECT COUNT(*) FROM superadmins)  AS superadmins
        ");

        $tenantStats = DB::table('tenants')
            ->selectRaw('status, COUNT(*) AS cnt')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $todayStats = DB::selectOne("
            SELECT COUNT(*) AS txn_count, COALESCE(SUM(amount),0) AS txn_volume
            FROM transactions
            WHERE created_at >= CURRENT_DATE
        ");

        $weeklyActivity = DB::select("
            SELECT TO_CHAR(DATE_TRUNC('day', created_at), 'Dy DD Mon') AS day_label,
                   COUNT(*) AS txn_count,
                   COALESCE(SUM(amount), 0) AS volume
            FROM transactions
            WHERE created_at >= NOW() - INTERVAL '7 days'
            GROUP BY DATE_TRUNC('day', created_at)
            ORDER BY DATE_TRUNC('day', created_at) ASC
        ");

        $topTenants = DB::select("
            SELECT t.id, t.name, t.short_name, t.status,
                   COUNT(tx.id)                       AS txn_count,
                   COALESCE(SUM(tx.amount), 0)        AS txn_volume,
                   (SELECT COUNT(*) FROM customers c WHERE c.tenant_id = t.id) AS customer_count
            FROM tenants t
            LEFT JOIN transactions tx ON tx.tenant_id = t.id
                AND tx.created_at >= NOW() - INTERVAL '30 days'
            GROUP BY t.id, t.name, t.short_name, t.status
            ORDER BY txn_count DESC
            LIMIT 8
        ");

        $pendingKyc   = DB::table('customers')->where('kyc_status', 'manual_review')->count();
        $pendingLoans = DB::table('loans')->where('status', 'pending')->count();

        return view('system-health', compact(
            'phpVersion', 'laravelVersion', 'dbStatus', 'dbSize',
            'counts', 'tenantStats', 'todayStats', 'weeklyActivity',
            'topTenants', 'pendingKyc', 'pendingLoans'
        ));
    }
}
