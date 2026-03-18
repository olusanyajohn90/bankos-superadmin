<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SystemHealthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\ReportsController;

Route::middleware('guest:superadmin')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::middleware('auth:superadmin')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Tenants
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{id}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{id}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{id}', [TenantController::class, 'update'])->name('tenants.update');
    Route::post('/tenants/{id}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('/tenants/{id}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    Route::post('/tenants/{id}/impersonate', [ImpersonateController::class, 'generate'])->name('tenants.impersonate');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [AnalyticsController::class, 'data'])->name('analytics.data');
    Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers.index');
    Route::get('/transactions', [AnalyticsController::class, 'transactions'])->name('transactions.index');

    // System Health
    Route::get('/system-health', [SystemHealthController::class, 'index'])->name('system-health.index');

    // Global Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Audit Log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // KYC Escalations
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('/kyc/{customerId}', [KycController::class, 'show'])->name('kyc.show');

    // Regulatory Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/deposits', [ReportsController::class, 'depositsReport'])->name('reports.deposits');
    Route::get('/reports/loan-portfolio', [ReportsController::class, 'loanPortfolioReport'])->name('reports.loan-portfolio');
    Route::get('/reports/customer-growth', [ReportsController::class, 'customerGrowthReport'])->name('reports.customer-growth');
    Route::get('/reports/transaction-summary', [ReportsController::class, 'transactionSummaryReport'])->name('reports.transaction-summary');
});
