@extends('layouts.app')

@section('title', 'System Health')
@section('page-title', 'System Health')
@section('page-subtitle', 'Platform status and infrastructure overview')

@section('content')

{{-- Status Banner --}}
<div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl {{ $dbStatus === 'connected' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
    <div class="w-2.5 h-2.5 rounded-full {{ $dbStatus === 'connected' ? 'bg-green-500' : 'bg-red-500' }} animate-pulse"></div>
    <div>
        <p class="text-sm font-semibold {{ $dbStatus === 'connected' ? 'text-green-800' : 'text-red-800' }}">
            Database {{ $dbStatus === 'connected' ? 'Connected' : 'Connection Error' }}
        </p>
        <p class="text-xs {{ $dbStatus === 'connected' ? 'text-green-600' : 'text-red-600' }}">
            PostgreSQL · Size: {{ $dbSize->size ?? 'unknown' }} · PHP {{ $phpVersion }} · Laravel {{ $laravelVersion }}
        </p>
    </div>
    <div class="ml-auto text-right">
        <p class="text-xs text-slate-500">Last checked</p>
        <p class="text-xs font-medium text-slate-700">{{ now()->format('H:i:s') }}</p>
    </div>
</div>

{{-- Platform Counts --}}
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @foreach ([
        ['Tenants',      $counts->tenants,      'bg-purple-50', 'text-purple-600'],
        ['Customers',    $counts->customers,    'bg-blue-50',   'text-blue-600'],
        ['Accounts',     $counts->accounts,     'bg-cyan-50',   'text-cyan-600'],
        ['Transactions', $counts->transactions, 'bg-emerald-50','text-emerald-600'],
        ['Loans',        $counts->loans,        'bg-amber-50',  'text-amber-600'],
        ['SuperAdmins',  $counts->superadmins,  'bg-rose-50',   'text-rose-600'],
    ] as [$label, $value, $bg, $text])
    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
        <div class="w-9 h-9 {{ $bg }} rounded-lg flex items-center justify-center mx-auto mb-2">
            <span class="{{ $text }} text-sm font-bold">{{ $value > 999 ? round($value/1000,1).'k' : $value }}</span>
        </div>
        <p class="text-lg font-bold text-slate-900">{{ number_format($value) }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ $label }}</p>
    </div>
    @endforeach
</div>

{{-- Today Stats + Pending Actions --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Today's Activity</p>
        <p class="text-3xl font-bold text-slate-900">{{ number_format($todayStats->txn_count) }}</p>
        <p class="text-xs text-slate-500 mt-1">Transactions today</p>
        <p class="text-base font-semibold text-emerald-600 mt-3">₦{{ number_format($todayStats->txn_volume, 2) }}</p>
        <p class="text-xs text-slate-500">Total volume</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Pending Actions</p>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">KYC Reviews</span>
                <span class="text-sm font-bold {{ $pendingKyc > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $pendingKyc }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Loan Applications</span>
                <span class="text-sm font-bold {{ $pendingLoans > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $pendingLoans }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Active Tenants</span>
                <span class="text-sm font-bold text-green-600">{{ $tenantStats['active']->cnt ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Suspended</span>
                <span class="text-sm font-bold {{ ($tenantStats['suspended']->cnt ?? 0) > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $tenantStats['suspended']->cnt ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Environment</p>
        <div class="space-y-2.5">
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">PHP</span>
                <span class="font-mono font-medium text-slate-800">{{ $phpVersion }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Laravel</span>
                <span class="font-mono font-medium text-slate-800">{{ $laravelVersion }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">DB Size</span>
                <span class="font-mono font-medium text-slate-800">{{ $dbSize->size ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Environment</span>
                <span class="font-mono font-medium text-slate-800">{{ app()->environment() }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Timezone</span>
                <span class="font-mono font-medium text-slate-800">{{ config('app.timezone') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- 7-Day Transaction Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-sm font-semibold text-slate-800 mb-4">7-Day Transaction Activity</p>
        @if(count($weeklyActivity) > 0)
        <div class="space-y-2">
            @php $maxVol = collect($weeklyActivity)->max('volume') ?: 1; @endphp
            @foreach($weeklyActivity as $day)
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 w-20 flex-shrink-0">{{ $day->day_label }}</span>
                <div class="flex-1 bg-slate-100 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, ($day->volume / $maxVol) * 100) }}%"></div>
                </div>
                <span class="text-xs font-medium text-slate-700 w-12 text-right">{{ $day->txn_count }}</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-slate-400 text-center py-8">No transactions in the last 7 days</p>
        @endif
    </div>

    {{-- Top Tenants by Activity --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <p class="text-sm font-semibold text-slate-800 mb-4">Top Tenants — Last 30 Days</p>
        <div class="space-y-2">
            @forelse($topTenants as $tenant)
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-slate-600">{{ strtoupper(substr($tenant->short_name,0,2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-800 truncate">{{ $tenant->name }}</p>
                    <p class="text-[10px] text-slate-400">{{ number_format($tenant->customer_count) }} customers</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs font-semibold text-slate-800">{{ number_format($tenant->txn_count) }} txns</p>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $tenant->status === 'active' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">{{ $tenant->status }}</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-slate-400 text-center py-4">No tenant data</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
