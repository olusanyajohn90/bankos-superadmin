<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImpersonateController extends Controller
{
    public function generate($tenantId)
    {
        $tenant = DB::table('tenants')->where('id', $tenantId)->first();
        if (!$tenant) {
            return back()->with('error', 'Tenant not found.');
        }

        // Find first active user (admin) in this tenant
        $adminUser = DB::table('users')
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('created_at')
            ->first();

        if (!$adminUser) {
            return back()->with('error', 'No active admin user found for ' . $tenant->name . '.');
        }

        // Store a short-lived impersonation token in the shared DB
        DB::table('superadmin_impersonations')->updateOrInsert(
            ['tenant_id' => $tenantId, 'used_at' => null],
            [
                'id'         => (string) Str::uuid(),
                'token'      => $token = Str::random(64),
                'user_id'    => $adminUser->id,
                'tenant_id'  => $tenantId,
                'created_by' => auth('superadmin')->user()->name,
                'expires_at' => now()->addMinutes(10),
                'used_at'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Redirect to the bankos admin app's impersonation endpoint
        $adminAppUrl = rtrim(config('services.bankos_admin_url', 'http://bankos.test'), '/');
        return redirect()->away("{$adminAppUrl}/sa-impersonate/{$token}");
    }
}
