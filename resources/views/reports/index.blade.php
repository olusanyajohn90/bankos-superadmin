@extends('layouts.app')

@section('title', 'Regulatory Reports')
@section('page-title', 'Regulatory Reports')
@section('page-subtitle', 'Generate and export platform-wide compliance reports')

@section('content')

{{-- Platform Snapshot --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['Total Deposits', '₦'.number_format($summary->total_deposits,2), 'bg-emerald-50', 'text-emerald-600'],
        ['Loan Book', '₦'.number_format($summary->total_loans,2), 'bg-blue-50', 'text-blue-600'],
        ['Active Customers', number_format($summary->active_customers), 'bg-purple-50', 'text-purple-600'],
        ['Txns This Month', number_format($summary->txns_this_month), 'bg-amber-50', 'text-amber-600'],
    ] as [$label, $value, $bg, $text])
    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
        <p class="text-xl font-bold {{ $text }}">{{ $value }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ $label }}</p>
    </div>
    @endforeach
</div>

{{-- Report Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Deposits Report --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6" x-data="{ open: false }">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Deposits Summary</h3>
                <p class="text-xs text-slate-500 mt-0.5">Monthly deposit volume by tenant with transaction counts</p>
            </div>
        </div>
        <button @click="open = !open" class="w-full text-left text-xs text-blue-600 font-medium mb-3 flex items-center gap-1">
            <span x-text="open ? 'Hide options' : 'Configure report'"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="transition-transform" :class="open ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="open" x-transition class="space-y-3 mb-4">
            <form method="GET" action="/reports/deposits" class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="text-xs text-slate-500 block mb-1">From</label><input type="date" name="from" value="{{ now()->startOfYear()->toDateString() }}" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="text-xs text-slate-500 block mb-1">To</label><input type="date" name="to" value="{{ now()->toDateString() }}" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
                </div>
                <select name="tenant_id" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Tenants</option>
                    @foreach($tenants as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">View Report</button>
                    <button type="submit" name="export" value="1" class="flex-1 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition-colors">Export CSV</button>
                </div>
            </form>
        </div>
        <form method="GET" action="/reports/deposits" class="flex gap-2" x-show="!open">
            <input type="hidden" name="from" value="{{ now()->startOfYear()->toDateString() }}">
            <input type="hidden" name="to" value="{{ now()->toDateString() }}">
            <button type="submit" class="flex-1 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Quick View</button>
        </form>
    </div>

    {{-- Loan Portfolio Report --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6" x-data="{ open: false }">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-600"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Loan Portfolio</h3>
                <p class="text-xs text-slate-500 mt-0.5">Outstanding balances, PAR ratio and NPL by tenant</p>
            </div>
        </div>
        <button @click="open = !open" class="w-full text-left text-xs text-blue-600 font-medium mb-3 flex items-center gap-1">
            <span x-text="open ? 'Hide options' : 'Configure report'"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="transition-transform" :class="open ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="open" x-transition class="space-y-3 mb-4">
            <form method="GET" action="/reports/loan-portfolio" class="space-y-3">
                <select name="tenant_id" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Tenants</option>
                    @foreach($tenants as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">View Report</button>
                    <button type="submit" name="export" value="1" class="flex-1 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition-colors">Export CSV</button>
                </div>
            </form>
        </div>
        <form method="GET" action="/reports/loan-portfolio" class="flex gap-2" x-show="!open">
            <button type="submit" class="flex-1 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Quick View</button>
        </form>
    </div>

    {{-- Customer Growth Report --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6" x-data="{ open: false }">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-purple-600"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Customer Growth</h3>
                <p class="text-xs text-slate-500 mt-0.5">Monthly new customer registrations and KYC approval rates</p>
            </div>
        </div>
        <button @click="open = !open" class="w-full text-left text-xs text-blue-600 font-medium mb-3 flex items-center gap-1">
            <span x-text="open ? 'Hide options' : 'Configure report'"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="transition-transform" :class="open ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="open" x-transition class="space-y-3 mb-4">
            <form method="GET" action="/reports/customer-growth" class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="text-xs text-slate-500 block mb-1">From</label><input type="date" name="from" value="{{ now()->subMonths(6)->toDateString() }}" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="text-xs text-slate-500 block mb-1">To</label><input type="date" name="to" value="{{ now()->toDateString() }}" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
                </div>
                <select name="tenant_id" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Tenants</option>
                    @foreach($tenants as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">View Report</button>
                    <button type="submit" name="export" value="1" class="flex-1 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition-colors">Export CSV</button>
                </div>
            </form>
        </div>
        <form method="GET" action="/reports/customer-growth" class="flex gap-2" x-show="!open">
            <button type="submit" class="flex-1 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">Quick View</button>
        </form>
    </div>

    {{-- Transaction Summary Report --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6" x-data="{ open: false }">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-amber-600"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Transaction Summary</h3>
                <p class="text-xs text-slate-500 mt-0.5">Volume and count by transaction type and tenant</p>
            </div>
        </div>
        <button @click="open = !open" class="w-full text-left text-xs text-blue-600 font-medium mb-3 flex items-center gap-1">
            <span x-text="open ? 'Hide options' : 'Configure report'"></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="transition-transform" :class="open ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div x-show="open" x-transition class="space-y-3 mb-4">
            <form method="GET" action="/reports/transaction-summary" class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="text-xs text-slate-500 block mb-1">From</label><input type="date" name="from" value="{{ now()->startOfMonth()->toDateString() }}" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
                    <div><label class="text-xs text-slate-500 block mb-1">To</label><input type="date" name="to" value="{{ now()->toDateString() }}" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></div>
                </div>
                <select name="tenant_id" class="w-full px-2 py-1.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Tenants</option>
                    @foreach($tenants as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">View Report</button>
                    <button type="submit" name="export" value="1" class="flex-1 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition-colors">Export CSV</button>
                </div>
            </form>
        </div>
        <form method="GET" action="/reports/transaction-summary" class="flex gap-2" x-show="!open">
            <button type="submit" class="flex-1 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">Quick View</button>
        </form>
    </div>

</div>
@endsection
