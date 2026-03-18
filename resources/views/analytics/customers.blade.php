@extends('layouts.app')

@section('title', 'All Customers')
@section('page-title', 'All Customers')
@section('page-subtitle', 'Across all tenants')

@section('content')

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-5">
    <form method="GET" action="{{ route('customers.index') }}" class="flex flex-wrap gap-3 items-end">
        {{-- Search --}}
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Name, phone, email, customer #"
                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        {{-- Tenant --}}
        <div class="w-44">
            <label class="block text-xs font-medium text-slate-600 mb-1">Tenant</label>
            <select name="tenant_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Tenants</option>
                @foreach($tenants as $t)
                    <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div class="w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>

        {{-- KYC Tier --}}
        <div class="w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">KYC Tier</label>
            <select name="kyc_tier" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Tiers</option>
                <option value="level_1" {{ request('kyc_tier') == 'level_1' ? 'selected' : '' }}>Level 1</option>
                <option value="level_2" {{ request('kyc_tier') == 'level_2' ? 'selected' : '' }}>Level 2</option>
                <option value="level_3" {{ request('kyc_tier') == 'level_3' ? 'selected' : '' }}>Level 3</option>
            </select>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'tenant_id', 'status', 'kyc_tier']))
            <a href="{{ route('customers.index') }}"
               class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Stats row --}}
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-slate-600">
        Showing <span class="font-semibold text-slate-900">{{ number_format($customers->firstItem() ?? 0) }}–{{ number_format($customers->lastItem() ?? 0) }}</span>
        of <span class="font-semibold text-slate-900">{{ number_format($totalCount) }}</span> customers
    </p>
    <p class="text-xs text-slate-400">Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}</p>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Customer</th>
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Contact</th>
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Tenant</th>
                    <th class="text-center text-xs font-semibold text-slate-500 px-4 py-3">KYC Tier</th>
                    <th class="text-center text-xs font-semibold text-slate-500 px-4 py-3">Status</th>
                    <th class="text-right text-xs font-semibold text-slate-500 px-4 py-3">Accounts</th>
                    <th class="text-right text-xs font-semibold text-slate-500 px-4 py-3">Balance (₦)</th>
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customers as $customer)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-900">{{ $customer->full_name }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ $customer->customer_number }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-slate-700">{{ $customer->phone }}</p>
                        <p class="text-xs text-slate-400">{{ $customer->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $customer->tenant_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $kycColors = [
                                'level_1' => 'bg-slate-100 text-slate-600',
                                'level_2' => 'bg-blue-100 text-blue-700',
                                'level_3' => 'bg-green-100 text-green-700',
                            ];
                            $kycLabels = ['level_1' => 'L1', 'level_2' => 'L2', 'level_3' => 'L3'];
                            $kycColor = $kycColors[$customer->kyc_tier] ?? 'bg-slate-100 text-slate-500';
                            $kycLabel = $kycLabels[$customer->kyc_tier] ?? ($customer->kyc_tier ?? '—');
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $kycColor }}">{{ $kycLabel }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = [
                                'active'    => 'bg-green-100 text-green-700',
                                'inactive'  => 'bg-red-100 text-red-600',
                                'suspended' => 'bg-yellow-100 text-yellow-700',
                            ];
                            $sColor = $statusColors[$customer->status] ?? 'bg-slate-100 text-slate-500';
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $sColor }} capitalize">{{ $customer->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-right text-slate-700">{{ number_format($customer->account_count) }}</td>
                    <td class="px-4 py-3 text-right font-medium text-slate-900">{{ number_format($customer->total_balance, 2) }}</td>
                    <td class="px-4 py-3 text-xs text-slate-500">{{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        No customers found matching your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($customers->hasPages())
    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
        {{ $customers->links() }}
    </div>
    @endif
</div>

@endsection
