@extends('layouts.app')

@section('title', 'KYC Queue')
@section('page-title', 'KYC Escalations')
@section('page-subtitle', 'Cross-tenant identity verification queue')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Pending Review', number_format($stats->pending), 'bg-amber-50', 'text-amber-600', 'border-amber-200'],
        ['Pending Docs', number_format($stats->pending_docs), 'bg-orange-50', 'text-orange-600', 'border-orange-200'],
        ['Approved (7d)', number_format($stats->approved_7d), 'bg-green-50', 'text-green-600', 'border-green-200'],
        ['Rejected (7d)', number_format($stats->rejected_7d), 'bg-red-50', 'text-red-600', 'border-red-200'],
    ] as [$label, $value, $bg, $text, $border])
    <div class="bg-white rounded-xl border {{ $border }} p-4 text-center">
        <p class="text-2xl font-bold {{ $text }}">{{ $value }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ $label }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
    {{-- Filters --}}
    <form method="GET" action="/kyc" class="lg:col-span-3 bg-white rounded-xl border border-slate-200 p-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <select name="tenant_id" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">All Tenants</option>
                @foreach($tenants as $t)
                <option value="{{ $t->id }}" {{ request('tenant_id') === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
            <select name="kyc_tier" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">All Tiers</option>
                <option value="level_1" {{ request('kyc_tier') === 'level_1' ? 'selected' : '' }}>Level 1</option>
                <option value="level_2" {{ request('kyc_tier') === 'level_2' ? 'selected' : '' }}>Level 2</option>
                <option value="level_3" {{ request('kyc_tier') === 'level_3' ? 'selected' : '' }}>Level 3</option>
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name / phone..."
                   class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Filter</button>
                <a href="/kyc" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm rounded-lg hover:bg-slate-200">Clear</a>
            </div>
        </div>
    </form>

    {{-- By tenant breakdown --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">By Tenant</p>
        <div class="space-y-1.5">
            @foreach($byTenant->take(6) as $bt)
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-600 truncate">{{ $bt->tenant_name }}</span>
                <span class="font-semibold text-amber-600 ml-2">{{ $bt->cnt }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- KYC Queue Table --}}
<div class="bg-white rounded-xl border border-slate-200">
    <div class="px-5 py-3 border-b border-slate-100">
        <p class="text-sm font-semibold text-slate-700">Pending KYC Reviews <span class="text-slate-400 font-normal">({{ number_format($customers->total()) }})</span></p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Customer</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Contact</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Tier</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Docs</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Tenant</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Registered</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($customers as $c)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800">{{ $c->full_name }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ $c->customer_number }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-600">
                        <p>{{ $c->phone }}</p>
                        <p class="text-slate-400">{{ $c->email }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium">{{ str_replace('level_', 'L', $c->kyc_tier) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-semibold {{ $c->doc_count > 0 ? 'text-slate-800' : 'text-red-500' }}">{{ $c->doc_count }} doc{{ $c->doc_count != 1 ? 's' : '' }}</span>
                        @if($c->latest_doc)
                        <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($c->latest_doc)->diffForHumans() }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-600">{{ $c->tenant_name }}</td>
                    <td class="px-4 py-3 text-xs text-slate-400">{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <a href="/kyc/{{ $c->id }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors">
                            Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">No customers pending KYC review</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $customers->links() }}</div>
    @endif
</div>

@endsection
