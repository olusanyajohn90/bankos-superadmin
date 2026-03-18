@extends('layouts.app')

@section('title', 'Search')
@section('page-title', 'Global Search')
@section('page-subtitle', 'Search across all tenants, customers, accounts and transactions')

@section('content')

{{-- Search Bar --}}
<form method="GET" action="/search" class="mb-6">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>
        <input type="text" name="q" value="{{ $q }}" autofocus
               placeholder="Search by name, phone, email, account number, reference..."
               class="w-full pl-12 pr-4 py-3.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-5 text-sm font-medium text-white bg-blue-600 rounded-r-xl hover:bg-blue-700 transition-colors">
            Search
        </button>
    </div>
</form>

@if(strlen($q) < 2)
<div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <p class="text-slate-500 text-sm">Enter at least 2 characters to search</p>
</div>
@else

@php
    $total = $tenants->count() + $customers->count() + $accounts->count() + $transactions->count();
@endphp

<p class="text-sm text-slate-500 mb-4">
    Found <span class="font-semibold text-slate-800">{{ $total }}</span> result{{ $total !== 1 ? 's' : '' }} for "<span class="font-semibold text-blue-600">{{ $q }}</span>"
</p>

@if($total === 0)
<div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
    <p class="text-slate-500 text-sm">No results found. Try a different search term.</p>
</div>
@endif

{{-- Tenants --}}
@if($tenants->count())
<div class="bg-white rounded-xl border border-slate-200 mb-4">
    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-purple-500"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
        <p class="text-sm font-semibold text-slate-700">Tenants <span class="text-slate-400 font-normal">({{ $tenants->count() }})</span></p>
    </div>
    <div class="divide-y divide-slate-50">
        @foreach($tenants as $t)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50">
            <div>
                <a href="/tenants/{{ $t->id }}" class="text-sm font-medium text-blue-600 hover:underline">{{ $t->name }}</a>
                <p class="text-xs text-slate-400 mt-0.5">{{ $t->short_name }} · {{ $t->email }}</p>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $t->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">{{ $t->status }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Customers --}}
@if($customers->count())
<div class="bg-white rounded-xl border border-slate-200 mb-4">
    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-500"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <p class="text-sm font-semibold text-slate-700">Customers <span class="text-slate-400 font-normal">({{ $customers->count() }})</span></p>
    </div>
    <div class="divide-y divide-slate-50">
        @foreach($customers as $c)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50">
            <div>
                <p class="text-sm font-medium text-slate-800">{{ $c->full_name }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $c->customer_number }} · {{ $c->phone }} · {{ $c->tenant_name }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs px-2 py-0.5 rounded-full {{ $c->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $c->status }}</span>
                <p class="text-[10px] text-slate-400 mt-0.5">KYC Tier {{ $c->kyc_tier }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Accounts --}}
@if($accounts->count())
<div class="bg-white rounded-xl border border-slate-200 mb-4">
    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-cyan-500"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        <p class="text-sm font-semibold text-slate-700">Accounts <span class="text-slate-400 font-normal">({{ $accounts->count() }})</span></p>
    </div>
    <div class="divide-y divide-slate-50">
        @foreach($accounts as $a)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50">
            <div>
                <p class="text-sm font-medium text-slate-800 font-mono">{{ $a->account_number }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $a->customer_name }} · {{ $a->tenant_name }} · {{ ucfirst($a->type) }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-slate-800">₦{{ number_format($a->available_balance, 2) }}</p>
                <span class="text-[10px] px-2 py-0.5 rounded-full {{ $a->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $a->status }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Transactions --}}
@if($transactions->count())
<div class="bg-white rounded-xl border border-slate-200 mb-4">
    <div class="px-5 py-3 border-b border-slate-100 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-500"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        <p class="text-sm font-semibold text-slate-700">Transactions <span class="text-slate-400 font-normal">({{ $transactions->count() }})</span></p>
    </div>
    <div class="divide-y divide-slate-50">
        @foreach($transactions as $tx)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50">
            <div>
                <p class="text-sm font-mono font-medium text-slate-800">{{ $tx->reference }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $tx->customer_name }} · {{ $tx->tenant_name }} · {{ \Carbon\Carbon::parse($tx->created_at)->format('d M Y H:i') }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold {{ in_array($tx->type,['deposit','credit']) ? 'text-green-600' : 'text-slate-800' }}">
                    {{ in_array($tx->type,['deposit','credit']) ? '+' : '-' }}₦{{ number_format($tx->amount, 2) }}
                </p>
                <span class="text-[10px] text-slate-400 capitalize">{{ $tx->type }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endif

@endsection
