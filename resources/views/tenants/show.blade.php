@extends('layouts.app')

@section('title', $tenant->name . ' — Tenant')
@section('page-title', $tenant->name)
@section('page-subtitle', 'Tenant detail &mdash; ' . strtoupper(str_replace('_', ' ', $tenant->type ?? '')))

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 text-sm">
    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

{{-- New tenant credentials flash --}}
@if(session('admin_credentials'))
@php $creds = session('admin_credentials'); @endphp
<div class="mb-5 bg-blue-50 border border-blue-200 rounded-xl px-5 py-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        <div class="flex-1">
            <p class="font-semibold text-blue-900 text-sm">Admin Account Created — Save These Credentials</p>
            <p class="text-xs text-blue-600 mt-0.5 mb-3">Share the following login details with the tenant admin securely.</p>
            <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                <div><span class="text-blue-500 font-medium">Login URL:</span> <span class="text-blue-900">bankos.test (or configured domain)</span></div>
                <div><span class="text-blue-500 font-medium">Institution Code:</span> <span class="text-blue-900 font-mono font-bold">{{ $creds['short_name'] }}</span></div>
                <div><span class="text-blue-500 font-medium">Name:</span> <span class="text-blue-900">{{ $creds['name'] }}</span></div>
                <div><span class="text-blue-500 font-medium">Email:</span> <span class="text-blue-900">{{ $creds['email'] }}</span></div>
                <div><span class="text-blue-500 font-medium">Password:</span> <span class="text-blue-900 font-mono font-bold bg-blue-100 px-2 py-0.5 rounded">{{ $creds['password'] }}</span></div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-slate-400 mb-5">
    <a href="{{ route('tenants.index') }}" class="hover:text-slate-700 transition-colors">Tenants</a>
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-slate-600 font-medium">{{ $tenant->name }}</span>
</nav>

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg flex-shrink-0" style="background-color: {{ $tenant->primary_color ?? '#2563eb' }}">
            {{ strtoupper(substr($tenant->short_name, 0, 2)) }}
        </div>
        <div>
            <div class="flex items-center flex-wrap gap-2 mb-1">
                <h1 class="text-xl font-bold text-slate-900">{{ $tenant->name }}</h1>
                {{-- Status badge --}}
                @if($tenant->status === 'active')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active</span>
                @elseif($tenant->status === 'suspended')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Suspended</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ ucfirst($tenant->status ?? 'Unknown') }}</span>
                @endif
                {{-- Type badge --}}
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 capitalize">{{ str_replace('_', ' ', $tenant->type) }}</span>
                {{-- Plan badge --}}
                @php
                    $planColors = ['starter' => 'bg-slate-100 text-slate-600', 'growth' => 'bg-blue-100 text-blue-700', 'enterprise' => 'bg-purple-100 text-purple-700'];
                    $planColor = $planColors[$tenant->subscription_plan] ?? 'bg-slate-100 text-slate-600';
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $planColor }} capitalize">{{ $tenant->subscription_plan }}</span>
            </div>
            <p class="text-sm text-slate-400">Code: <code class="font-mono text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded text-xs">{{ $tenant->short_name }}</code>&nbsp;&nbsp;Created: {{ $tenant->created_at ? \Carbon\Carbon::parse($tenant->created_at)->format('d M Y') : '—' }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('tenants.edit', $tenant->id) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit
        </a>
        @if($tenant->status === 'active')
            <button onclick="document.getElementById('suspendModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Suspend
            </button>
        @else
            <form method="POST" action="{{ route('tenants.activate', $tenant->id) }}" onsubmit="return confirm('Reactivate this tenant?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Reactivate
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('tenants.impersonate', $tenant->id) }}"
              onsubmit="return confirm('This will open a 10-minute admin session for {{ addslashes($tenant->name) }}. Continue?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Login as Admin
            </button>
        </form>
    </div>
</div>

{{-- Suspension Warning --}}
@if($tenant->status === 'suspended')
<div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-5 py-4 flex items-start gap-3">
    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <div class="flex-1">
        <p class="font-semibold text-red-800 text-sm">Tenant Suspended</p>
        @if($tenant->suspended_at)
        <p class="text-xs text-red-600 mt-0.5">Suspended on {{ \Carbon\Carbon::parse($tenant->suspended_at)->format('d M Y, H:i') }}</p>
        @endif
        @if($tenant->suspension_reason)
        <p class="text-sm text-red-700 mt-2 bg-red-100 rounded-lg px-3 py-2">{{ $tenant->suspension_reason }}</p>
        @endif
    </div>
    <form method="POST" action="{{ route('tenants.activate', $tenant->id) }}" onsubmit="return confirm('Reactivate this tenant?')">
        @csrf
        <button type="submit" class="px-4 py-2 bg-white border border-red-300 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-50 transition-colors">
            Reactivate
        </button>
    </form>
</div>
@endif

{{-- KPI Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Customers</p>
        <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($customerCount) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Active Accounts</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($activeAccountCount) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Deposit Book</p>
        <p class="text-xl font-bold text-emerald-600 mt-1">₦{{ number_format($depositBook, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Loan Book</p>
        <p class="text-xl font-bold text-amber-600 mt-1">₦{{ number_format($loanBook, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Active Loans</p>
        <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($activeLoanCount) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-4 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Today's Txns</p>
        <p class="text-2xl font-bold text-slate-700 mt-1">{{ number_format($todayTxCount) }}</p>
    </div>
</div>

{{-- 2-Column Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Tenant Profile --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-900">Tenant Profile</h3>
        </div>
        <div class="px-5 py-4 space-y-3">
            @php
            $fields = [
                'Domain'              => $tenant->domain,
                'Portal Domain'       => $tenant->portal_domain,
                'CBN License'         => $tenant->cbn_license_number,
                'NIBSS Code'          => $tenant->nibss_institution_code,
                'Routing Number'      => $tenant->routing_number,
                'Contact Email'       => $tenant->contact_email,
                'Contact Phone'       => $tenant->contact_phone,
                'Primary Currency'    => $tenant->primary_currency,
                'Onboarding Step'     => $tenant->onboarding_step,
                'Onboarding Complete' => $tenant->onboarding_completed_at ? \Carbon\Carbon::parse($tenant->onboarding_completed_at)->format('d M Y H:i') : null,
            ];
            @endphp
            @foreach($fields as $label => $value)
            @if($value !== null && $value !== '')
            <div class="flex items-start justify-between py-1.5 border-b border-slate-50 last:border-0">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide w-36 flex-shrink-0">{{ $label }}</span>
                <span class="text-sm text-slate-800 text-right flex-1">{{ $value }}</span>
            </div>
            @endif
            @endforeach

            {{-- Branding colors --}}
            <div class="flex items-center justify-between py-1.5 border-b border-slate-50">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide w-36 flex-shrink-0">Primary Color</span>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded border border-slate-200" style="background: {{ $tenant->primary_color ?? '#2563eb' }}"></div>
                    <span class="text-sm text-slate-800 font-mono">{{ $tenant->primary_color ?? '#2563eb' }}</span>
                </div>
            </div>
            <div class="flex items-center justify-between py-1.5">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide w-36 flex-shrink-0">Secondary Color</span>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded border border-slate-200" style="background: {{ $tenant->secondary_color ?? '#0c2461' }}"></div>
                    <span class="text-sm text-slate-800 font-mono">{{ $tenant->secondary_color ?? '#0c2461' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin Users --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-900">Admin Users</h3>
            <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-1 rounded-full font-semibold">{{ $adminUsers->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            @if($adminUsers->count() > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-5 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Name / Email</th>
                        <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Last Login</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($adminUsers as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-900">{{ $user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->status === 'active')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Active</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ ucfirst($user->status ?? '—') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-400">
                            {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="px-5 py-10 text-center text-slate-400 text-sm">No admin users found.</div>
            @endif
        </div>
    </div>

</div>

{{-- Recent Customers --}}
<div class="bg-white rounded-xl border border-slate-200 mb-6">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-semibold text-slate-900">Recent Customers</h3>
        <span class="text-xs text-slate-400">Last 10</span>
    </div>
    <div class="overflow-x-auto">
        @if($recentCustomers->count() > 0)
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-5 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Phone</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">KYC Tier</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($recentCustomers as $customer)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-900">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $customer->phone ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Tier {{ $customer->kyc_tier ?? '0' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($customer->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ ucfirst($customer->status ?? '—') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $customer->created_at ? \Carbon\Carbon::parse($customer->created_at)->format('d M Y') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="px-5 py-10 text-center text-slate-400 text-sm">No customers yet.</div>
        @endif
    </div>
</div>

{{-- Recent Transactions --}}
<div class="bg-white rounded-xl border border-slate-200">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-semibold text-slate-900">Recent Transactions</h3>
        <span class="text-xs text-slate-400">Last 10</span>
    </div>
    <div class="overflow-x-auto">
        @if($recentTransactions->count() > 0)
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-5 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Reference</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                    <th class="text-right px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Amount</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($recentTransactions as $tx)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-mono text-xs text-slate-600">{{ $tx->reference }}</td>
                    <td class="px-4 py-3 text-slate-500 capitalize">{{ str_replace('_', ' ', $tx->type) }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ $tx->currency ?? 'NGN' }} {{ number_format($tx->amount, 2) }}</td>
                    <td class="px-4 py-3 text-slate-500 max-w-48 truncate">{{ $tx->description ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($tx->status === 'completed' || $tx->status === 'success')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">{{ ucfirst($tx->status) }}</span>
                        @elseif($tx->status === 'failed' || $tx->status === 'reversed')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ ucfirst($tx->status) }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">{{ ucfirst($tx->status ?? '—') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ $tx->created_at ? \Carbon\Carbon::parse($tx->created_at)->format('d M Y H:i') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="px-5 py-10 text-center text-slate-400 text-sm">No transactions yet.</div>
        @endif
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('suspendModal').classList.add('hidden')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative">
            <div class="px-6 pt-6 pb-4 border-b border-slate-100 flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Suspend Tenant</h2>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $tenant->name }}</p>
                </div>
                <button onclick="document.getElementById('suspendModal').classList.add('hidden')" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('tenants.suspend', $tenant->id) }}">
                @csrf
                <div class="px-6 py-5">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-sm text-red-700">Suspending will immediately lock out all staff and customers of this tenant. This can be reversed.</p>
                    </div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Suspension Reason <span class="text-red-500">*</span></label>
                    <textarea
                        name="suspension_reason"
                        rows="4"
                        required
                        placeholder="e.g. Regulatory compliance issue, subscription expired..."
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                    ></textarea>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" onclick="document.getElementById('suspendModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors">
                        Suspend Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
