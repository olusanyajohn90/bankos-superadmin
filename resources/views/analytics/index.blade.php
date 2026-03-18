@extends('layouts.app')

@section('title', 'Platform Analytics')
@section('page-title', 'Platform Analytics')
@section('page-subtitle', 'Real-time insights across all tenants')

@section('content')

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    {{-- Total Customers --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">All Tenants</span>
        </div>
        <p class="text-2xl font-bold text-slate-900">{{ number_format($platformTotals->total_customers) }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Customers</p>
    </div>

    {{-- Total Deposits --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Active Accts</span>
        </div>
        <p class="text-2xl font-bold text-slate-900">₦{{ number_format($platformTotals->total_deposits, 2) }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Deposits</p>
    </div>

    {{-- Total Loan Book --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">Active + Overdue</span>
        </div>
        <p class="text-2xl font-bold text-slate-900">₦{{ number_format($platformTotals->total_loan_book, 2) }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Loan Book</p>
    </div>

    {{-- Total Transactions --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">All Time</span>
        </div>
        <p class="text-2xl font-bold text-slate-900">{{ number_format($platformTotals->total_transactions) }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Transactions</p>
    </div>
</div>

{{-- Row 2: Line chart + Doughnut chart --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-4 mb-6">
    {{-- Monthly Transaction Volume (60%) --}}
    <div class="xl:col-span-3 bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Monthly Transaction Volume</h3>
                <p class="text-xs text-slate-400 mt-0.5">Deposits vs Disbursements — last 6 months</p>
            </div>
        </div>
        <div class="relative h-56">
            <canvas id="volumeChart"></canvas>
        </div>
    </div>

    {{-- Loan Status Doughnut (40%) --}}
    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Loan Status Distribution</h3>
                <p class="text-xs text-slate-400 mt-0.5">By count</p>
            </div>
        </div>
        <div class="relative h-44 flex items-center justify-center">
            <canvas id="loanStatusChart"></canvas>
        </div>
        <div class="mt-3 space-y-1">
            @foreach($loanStatusDistribution as $ls)
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-600 capitalize">{{ $ls->status }}</span>
                <span class="font-semibold text-slate-900">{{ number_format($ls->loan_count) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Row 3: Customer Growth + New Loans Bar charts --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
    {{-- Customer Growth --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-slate-900">Customer Growth</h3>
            <p class="text-xs text-slate-400 mt-0.5">New customers per month — last 12 months</p>
        </div>
        <div class="relative h-52">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    {{-- New Loans Originated --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-slate-900">New Loans Originated</h3>
            <p class="text-xs text-slate-400 mt-0.5">Count per month — last 6 months</p>
        </div>
        <div class="relative h-52">
            <canvas id="loansOriginatedChart"></canvas>
        </div>
    </div>
</div>

{{-- Row 4: Transaction Type Breakdown + Account Type Distribution --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
    {{-- Transaction Type Breakdown --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-slate-900">Transaction Type Breakdown</h3>
            <p class="text-xs text-slate-400 mt-0.5">Last 30 days</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left text-xs font-semibold text-slate-500 pb-2">Type</th>
                    <th class="text-right text-xs font-semibold text-slate-500 pb-2">Count</th>
                    <th class="text-right text-xs font-semibold text-slate-500 pb-2">Volume (₦)</th>
                    <th class="text-right text-xs font-semibold text-slate-500 pb-2">% Vol</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($txnTypeBreakdown as $row)
                <tr class="hover:bg-slate-50">
                    <td class="py-2">
                        @php
                            $typeColors = [
                                'deposit' => 'bg-green-100 text-green-700',
                                'withdrawal' => 'bg-red-100 text-red-700',
                                'transfer' => 'bg-blue-100 text-blue-700',
                                'fee' => 'bg-slate-100 text-slate-600',
                                'repayment' => 'bg-purple-100 text-purple-700',
                                'interest' => 'bg-teal-100 text-teal-700',
                                'disbursement' => 'bg-orange-100 text-orange-700',
                            ];
                            $color = $typeColors[$row->type] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $color }} capitalize">{{ $row->type }}</span>
                    </td>
                    <td class="py-2 text-right text-slate-700 font-medium">{{ number_format($row->txn_count) }}</td>
                    <td class="py-2 text-right text-slate-700">{{ number_format($row->total_amount, 2) }}</td>
                    <td class="py-2 text-right text-slate-500">
                        {{ $totalVolume30d > 0 ? number_format(($row->total_amount / $totalVolume30d) * 100, 1) : '0.0' }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-slate-400 text-xs">No transactions in the last 30 days</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Account Type Distribution --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-slate-900">Account Type Distribution</h3>
            <p class="text-xs text-slate-400 mt-0.5">All accounts on platform</p>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left text-xs font-semibold text-slate-500 pb-2">Type</th>
                    <th class="text-right text-xs font-semibold text-slate-500 pb-2">Accounts</th>
                    <th class="text-right text-xs font-semibold text-slate-500 pb-2">Total Balance (₦)</th>
                    <th class="text-right text-xs font-semibold text-slate-500 pb-2">Avg Balance (₦)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($accountTypeDistribution as $row)
                <tr class="hover:bg-slate-50">
                    <td class="py-2">
                        <span class="capitalize text-slate-700 font-medium">{{ $row->type }}</span>
                    </td>
                    <td class="py-2 text-right text-slate-700">{{ number_format($row->account_count) }}</td>
                    <td class="py-2 text-right text-slate-700">{{ number_format($row->total_balance, 2) }}</td>
                    <td class="py-2 text-right text-slate-500">
                        {{ $row->account_count > 0 ? number_format($row->total_balance / $row->account_count, 2) : '0.00' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-6 text-center text-slate-400 text-xs">No account data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-500">Avg Account Balance</span>
            <span class="text-sm font-semibold text-slate-900">₦{{ number_format($platformTotals->avg_account_balance, 2) }}</span>
        </div>
    </div>
</div>

{{-- Row 5: Top 10 Customers --}}
<div class="bg-white rounded-xl border border-slate-200 p-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-slate-900">Top 10 Customers by Total Balance</h3>
            <p class="text-xs text-slate-400 mt-0.5">Ranked by sum of available balance across all accounts</p>
        </div>
        <a href="{{ route('customers.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">View all customers &rarr;</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left text-xs font-semibold text-slate-500 pb-2 w-8">#</th>
                    <th class="text-left text-xs font-semibold text-slate-500 pb-2">Customer</th>
                    <th class="text-left text-xs font-semibold text-slate-500 pb-2">Tenant</th>
                    <th class="text-left text-xs font-semibold text-slate-500 pb-2">Phone</th>
                    <th class="text-right text-xs font-semibold text-slate-500 pb-2">Accounts</th>
                    <th class="text-right text-xs font-semibold text-slate-500 pb-2">Total Balance (₦)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($topCustomers as $i => $cust)
                <tr class="hover:bg-slate-50">
                    <td class="py-2.5">
                        <span class="text-xs font-bold {{ $i < 3 ? 'text-blue-600' : 'text-slate-400' }}">{{ $i + 1 }}</span>
                    </td>
                    <td class="py-2.5">
                        <p class="font-medium text-slate-900">{{ $cust->full_name }}</p>
                        <p class="text-xs text-slate-400">{{ $cust->customer_number }}</p>
                    </td>
                    <td class="py-2.5 text-slate-600 text-xs">{{ $cust->tenant_name ?? '—' }}</td>
                    <td class="py-2.5 text-slate-600">{{ $cust->phone }}</td>
                    <td class="py-2.5 text-right text-slate-700">{{ number_format($cust->account_count) }}</td>
                    <td class="py-2.5 text-right font-semibold text-slate-900">{{ number_format($cust->total_balance, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 text-xs">No customer data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    'use strict';

    // ── Data from PHP ──────────────────────────────────────────────
    const volumeData   = @json($monthlyVolume);
    const growthData   = @json($monthlyGrowth);
    const loansData    = @json($monthlyLoans);
    const loanStatus   = @json($loanStatusDistribution);

    // ── Helper ─────────────────────────────────────────────────────
    function fmtMonth(ym) {
        const [y, m] = ym.split('-');
        return new Date(y, m - 1).toLocaleString('en-GB', { month: 'short', year: '2-digit' });
    }

    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { labels: { font: { size: 11 }, boxWidth: 12 } } },
    };

    // ── 1. Monthly Volume Line Chart ───────────────────────────────
    const volumeCtx = document.getElementById('volumeChart');
    if (volumeCtx) {
        new Chart(volumeCtx, {
            type: 'line',
            data: {
                labels: volumeData.map(d => fmtMonth(d.month)),
                datasets: [
                    {
                        label: 'Deposits (₦)',
                        data: volumeData.map(d => parseFloat(d.deposits) || 0),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        borderWidth: 2,
                    },
                    {
                        label: 'Disbursements (₦)',
                        data: volumeData.map(d => parseFloat(d.disbursements) || 0),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                ...chartDefaults,
                scales: {
                    y: {
                        ticks: {
                            font: { size: 10 },
                            callback: v => '₦' + (v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'K' : v),
                        },
                        grid: { color: 'rgba(0,0,0,0.04)' },
                    },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } },
                },
            },
        });
    }

    // ── 2. Loan Status Doughnut ────────────────────────────────────
    const loanStatusCtx = document.getElementById('loanStatusChart');
    if (loanStatusCtx) {
        const statusColors = { active: '#10b981', overdue: '#f59e0b', closed: '#94a3b8', defaulted: '#ef4444', pending: '#6366f1' };
        new Chart(loanStatusCtx, {
            type: 'doughnut',
            data: {
                labels: loanStatus.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
                datasets: [{
                    data: loanStatus.map(d => parseInt(d.loan_count)),
                    backgroundColor: loanStatus.map(d => statusColors[d.status] || '#94a3b8'),
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                ...chartDefaults,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10, padding: 8 } },
                },
            },
        });
    }

    // ── 3. Customer Growth Bar ─────────────────────────────────────
    const growthCtx = document.getElementById('growthChart');
    if (growthCtx) {
        new Chart(growthCtx, {
            type: 'bar',
            data: {
                labels: growthData.map(d => fmtMonth(d.month)),
                datasets: [{
                    label: 'New Customers',
                    data: growthData.map(d => parseInt(d.new_customers)),
                    backgroundColor: 'rgba(99,102,241,0.75)',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                ...chartDefaults,
                scales: {
                    y: {
                        ticks: { font: { size: 10 }, precision: 0 },
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        beginAtZero: true,
                    },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } },
                },
                plugins: { legend: { display: false } },
            },
        });
    }

    // ── 4. New Loans Originated Bar ────────────────────────────────
    const loansOriginatedCtx = document.getElementById('loansOriginatedChart');
    if (loansOriginatedCtx) {
        new Chart(loansOriginatedCtx, {
            type: 'bar',
            data: {
                labels: loansData.map(d => fmtMonth(d.month)),
                datasets: [{
                    label: 'New Loans',
                    data: loansData.map(d => parseInt(d.new_loans)),
                    backgroundColor: 'rgba(16,185,129,0.75)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                ...chartDefaults,
                scales: {
                    y: {
                        ticks: { font: { size: 10 }, precision: 0 },
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        beginAtZero: true,
                    },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } },
                },
                plugins: { legend: { display: false } },
            },
        });
    }
})();
</script>
@endpush
