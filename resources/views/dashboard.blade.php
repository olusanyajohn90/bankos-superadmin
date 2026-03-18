@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Platform Dashboard')
@section('page-subtitle', 'Real-time overview of all tenants and platform activity')

@section('content')

{{-- ===== ROW 1: Primary KPI Cards ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    {{-- Total Tenants --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-start gap-4">
        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Active Tenants</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalTenants) }}</p>
            <p class="text-xs text-emerald-600 font-medium mt-1">Institutions on platform</p>
        </div>
    </div>

    {{-- Total Customers --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-start gap-4">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Customers</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalCustomers) }}</p>
            <p class="text-xs text-blue-600 font-medium mt-1">Across all tenants</p>
        </div>
    </div>

    {{-- Total Deposits --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-start gap-4">
        <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Deposits</p>
            <p class="text-2xl font-black text-slate-900 mt-1">&#8358;{{ number_format($totalDeposits, 2) }}</p>
            <p class="text-xs text-violet-600 font-medium mt-1">Savings + Current + Fixed</p>
        </div>
    </div>

    {{-- Total Loan Book --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-start gap-4">
        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Loan Book</p>
            <p class="text-2xl font-black text-slate-900 mt-1">&#8358;{{ number_format($totalLoanBook, 2) }}</p>
            <p class="text-xs text-orange-600 font-medium mt-1">Active + Overdue + Defaulted</p>
        </div>
    </div>

</div>

{{-- ===== ROW 2: Secondary KPI Cards ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    {{-- Today's Txn Count --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Today's Transactions</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($todayTxnCount) }}</p>
        </div>
    </div>

    {{-- Today's Volume --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Today's Volume</p>
            <p class="text-xl font-black text-slate-900">&#8358;{{ number_format($todayTxnVolume, 2) }}</p>
        </div>
    </div>

    {{-- Pending KYC --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending KYC</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($pendingKyc) }}</p>
        </div>
    </div>

    {{-- Pending Loan Apps --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pending Loan Apps</p>
            <p class="text-2xl font-black text-slate-900">{{ number_format($pendingLoans) }}</p>
        </div>
    </div>

</div>

{{-- ===== ROW 3: Tenant Overview Table ===== --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Tenant Overview</h2>
            <p class="text-xs text-slate-500 mt-0.5">All registered institutions on the platform</p>
        </div>
        <a href="/tenants" class="text-sm font-semibold text-blue-600 hover:text-blue-700">View all &rarr;</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Institution</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Plan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customers</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Deposit Book (&#8358;)</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold uppercase">{{ substr($tenant->short_name ?? $tenant->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900 text-sm">{{ $tenant->name }}</p>
                                <p class="text-xs text-slate-400">{{ $tenant->short_name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($tenant->subscription_plan === 'enterprise') bg-violet-100 text-violet-700
                            @elseif($tenant->subscription_plan === 'pro') bg-blue-100 text-blue-700
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ ucfirst($tenant->subscription_plan ?? 'basic') }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($tenant->status === 'active') bg-emerald-100 text-emerald-700
                            @elseif($tenant->status === 'suspended') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            <span class="w-1.5 h-1.5 rounded-full
                                @if($tenant->status === 'active') bg-emerald-500
                                @elseif($tenant->status === 'suspended') bg-red-500
                                @else bg-yellow-500 @endif"></span>
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right font-semibold text-slate-900">{{ number_format($tenant->customer_count) }}</td>
                    <td class="px-4 py-4 text-right font-semibold text-slate-900">{{ number_format($tenant->deposit_book, 2) }}</td>
                    <td class="px-4 py-4 text-center">
                        <a href="/tenants/{{ $tenant->id }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 text-xs font-semibold rounded-lg transition-colors duration-150">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto text-slate-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <p class="text-sm font-medium">No tenants found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== ROW 4: Recent Transactions Table ===== --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Recent Transactions</h2>
            <p class="text-xs text-slate-500 mt-0.5">Latest 10 transactions across all tenants</p>
        </div>
        <a href="/transactions" class="text-sm font-semibold text-blue-600 hover:text-blue-700">View all &rarr;</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Reference</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Customer</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Account</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Type</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Amount (&#8358;)</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($recentTransactions as $txn)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-6 py-3.5">
                        <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ $txn->reference }}</span>
                    </td>
                    <td class="px-4 py-3.5 font-medium text-slate-900">{{ $txn->customer_name }}</td>
                    <td class="px-4 py-3.5 font-mono text-xs text-slate-500">{{ $txn->account_number }}</td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold uppercase
                            @if(in_array($txn->type, ['credit','deposit'])) bg-emerald-100 text-emerald-700
                            @elseif(in_array($txn->type, ['debit','withdrawal'])) bg-red-100 text-red-700
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ $txn->type }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-right font-semibold text-slate-900">{{ number_format($txn->amount, 2) }}</td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($txn->status === 'completed' || $txn->status === 'success') bg-emerald-100 text-emerald-700
                            @elseif($txn->status === 'pending') bg-yellow-100 text-yellow-700
                            @elseif($txn->status === 'failed' || $txn->status === 'reversed') bg-red-100 text-red-700
                            @else bg-slate-100 text-slate-600 @endif">
                            {{ ucfirst($txn->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-right text-xs text-slate-500 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($txn->created_at)->format('d M Y, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto text-slate-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <p class="text-sm font-medium">No transactions yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
