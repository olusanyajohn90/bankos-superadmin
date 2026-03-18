<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('superadmins')->upsert([
            [
                'name'       => 'Platform Admin',
                'email'      => 'admin@bankos.io',
                'password'   => Hash::make('SuperAdmin@2026'),
                'role'       => 'superadmin',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ], ['email'], ['name', 'role', 'is_active', 'updated_at']);
    }
}
