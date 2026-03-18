<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superadmin_impersonations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('token', 64)->unique();
            $table->bigInteger('user_id');
            $table->uuid('tenant_id');
            $table->string('created_by');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('superadmin_impersonations');
    }
};
