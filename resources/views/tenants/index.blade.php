@extends('layouts.app')

@section('title', 'Tenant Management')
@section('page-title', 'Tenant Management')
@section('page-subtitle', 'Manage all institutional tenants on the bankOS platform')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 text-sm">
    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Tenants</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ $totalTenants }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Active</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $activeTenants }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Suspended</p>
        <p class="text-3xl font-bold text-red-600 mt-1">{{ $suspendedTenants }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 px-5 py-4">
        <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Customers</p>
        <p class="text-3xl font-bold text-blue-600 mt-1">{{ number_format($totalCustomers) }}</p>
    </div>
</div>

{{-- Filter + Actions Bar --}}
<div class="bg-white rounded-xl border border-slate-200 px-5 py-4 mb-5 flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <form method="GET" action="{{ route('tenants.index') }}" class="flex flex-1 gap-3 flex-wrap">
        <div class="relative flex-1 min-w-48">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by name or code..."
                class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
        </div>
        <select name="status" class="border border-slate-200 rounded-lg text-sm text-slate-700 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
            <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700 transition-colors">
            Filter
        </button>
        @if($search || $statusFilter !== 'all')
        <a href="{{ route('tenants.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition-colors">
            Clear
        </a>
        @endif
    </form>
    <a href="{{ route('tenants.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex-shrink-0">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Tenant
    </a>
</div>

{{-- Tenants Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tenant</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Code</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Plan</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Customers</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Deposit Book</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Loan Book</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Created</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-bold text-sm" style="background-color: {{ $tenant->primary_color ?? '#2563eb' }}">
                                {{ strtoupper(substr($tenant->short_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $tenant->name }}</p>
                                @if($tenant->domain)
                                <p class="text-xs text-slate-400">{{ $tenant->domain }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <code class="text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded font-mono">{{ $tenant->short_name }}</code>
                    </td>
                    <td class="px-4 py-4 text-slate-600 capitalize text-sm">
                        {{ str_replace('_', ' ', $tenant->type) }}
                    </td>
                    <td class="px-4 py-4">
                        @php
                            $planColors = [
                                'starter'    => 'bg-slate-100 text-slate-600',
                                'growth'     => 'bg-blue-100 text-blue-700',
                                'enterprise' => 'bg-purple-100 text-purple-700',
                            ];
                            $planColor = $planColors[$tenant->subscription_plan] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $planColor }} capitalize">
                            {{ $tenant->subscription_plan ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        @if($tenant->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active
                            </span>
                        @elseif($tenant->status === 'suspended')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Suspended
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                {{ ucfirst($tenant->status ?? 'Unknown') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-right font-medium text-slate-700">{{ number_format($tenant->customer_count) }}</td>
                    <td class="px-4 py-4 text-right font-medium text-slate-700">₦{{ number_format($tenant->deposit_book, 2) }}</td>
                    <td class="px-4 py-4 text-right font-medium text-slate-700">₦{{ number_format($tenant->loan_book, 2) }}</td>
                    <td class="px-4 py-4 text-slate-500 text-xs whitespace-nowrap">
                        {{ $tenant->created_at ? \Carbon\Carbon::parse($tenant->created_at)->format('d M Y') : '—' }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('tenants.show', $tenant->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                View
                            </a>
                            <a href="{{ route('tenants.edit', $tenant->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                Edit
                            </a>
                            @if($tenant->status === 'active')
                                <button
                                    onclick="openSuspendModal('{{ $tenant->id }}', '{{ addslashes($tenant->name) }}')"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                                >
                                    Suspend
                                </button>
                            @else
                                <form method="POST" action="{{ route('tenants.activate', $tenant->id) }}" onsubmit="return confirm('Reactivate this tenant?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-slate-400">
                            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <p class="text-sm font-medium">No tenants found</p>
                            <p class="text-xs">Try adjusting your search or filters.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tenants->count() > 0)
    <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 text-xs text-slate-400">
        Showing {{ $tenants->count() }} tenant{{ $tenants->count() !== 1 ? 's' : '' }}
    </div>
    @endif
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSuspendModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative">
            <div class="px-6 pt-6 pb-4 border-b border-slate-100">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Suspend Tenant</h2>
                        <p class="text-sm text-slate-500 mt-0.5" id="suspendModalSubtitle">Please provide a reason for suspension.</p>
                    </div>
                    <button onclick="closeSuspendModal()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form id="suspendForm" method="POST" action="">
                @csrf
                <div class="px-6 py-5">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <p class="text-sm text-red-700">Suspending a tenant will prevent all staff and customers from logging in. This action can be reversed.</p>
                    </div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Suspension Reason <span class="text-red-500">*</span></label>
                    <textarea
                        name="suspension_reason"
                        rows="4"
                        required
                        placeholder="e.g. Regulatory compliance issue, non-payment of subscription..."
                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                    ></textarea>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" onclick="closeSuspendModal()" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-colors">
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

<script>
function openSuspendModal(id, name) {
    document.getElementById('suspendModalSubtitle').textContent = 'Suspending: ' + name;
    document.getElementById('suspendForm').action = '/tenants/' + id + '/suspend';
    document.getElementById('suspendModal').classList.remove('hidden');
}
function closeSuspendModal() {
    document.getElementById('suspendModal').classList.add('hidden');
}
</script>

@endsection
