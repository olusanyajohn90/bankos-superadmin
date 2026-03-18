@extends('layouts.app')

@section('title', 'All Transactions')
@section('page-title', 'All Transactions')
@section('page-subtitle', 'Platform-wide transaction ledger')

@section('content')

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-5">
    <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap gap-3 items-end">
        {{-- Search --}}
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-slate-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Reference or customer name"
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

        {{-- Type --}}
        <div class="w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">Type</label>
            <select name="type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="fee" {{ request('type') == 'fee' ? 'selected' : '' }}>Fee</option>
                <option value="repayment" {{ request('type') == 'repayment' ? 'selected' : '' }}>Repayment</option>
                <option value="interest" {{ request('type') == 'interest' ? 'selected' : '' }}>Interest</option>
                <option value="disbursement" {{ request('type') == 'disbursement' ? 'selected' : '' }}>Disbursement</option>
            </select>
        </div>

        {{-- Status --}}
        <div class="w-32">
            <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        {{-- Date From --}}
        <div class="w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}"
                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Date To --}}
        <div class="w-36">
            <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}"
                   class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Buttons --}}
        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'tenant_id', 'type', 'status', 'from', 'to']))
            <a href="{{ route('transactions.index') }}"
               class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-lg transition-colors">
                Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Mini Stats Bar --}}
<div class="bg-white rounded-xl border border-slate-200 px-5 py-3 mb-5 flex items-center gap-6">
    <div>
        <span class="text-xs text-slate-500">Matching Transactions</span>
        <span class="ml-2 text-sm font-bold text-slate-900">{{ number_format($totalCount) }}</span>
    </div>
    <div class="w-px h-5 bg-slate-200"></div>
    <div>
        <span class="text-xs text-slate-500">Total Volume</span>
        <span class="ml-2 text-sm font-bold text-slate-900">₦{{ number_format($totalVolume, 2) }}</span>
    </div>
    <div class="ml-auto text-xs text-slate-400">
        Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Reference</th>
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Customer</th>
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Account</th>
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Tenant</th>
                    <th class="text-center text-xs font-semibold text-slate-500 px-4 py-3">Type</th>
                    <th class="text-right text-xs font-semibold text-slate-500 px-4 py-3">Amount (₦)</th>
                    <th class="text-center text-xs font-semibold text-slate-500 px-4 py-3">Status</th>
                    <th class="text-left text-xs font-semibold text-slate-500 px-4 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transactions as $txn)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3">
                        <p class="font-mono text-xs text-slate-700 font-medium">{{ $txn->reference }}</p>
                        @if($txn->description)
                        <p class="text-xs text-slate-400 truncate max-w-[160px]">{{ $txn->description }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-700">{{ $txn->customer_name }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $txn->account_number ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-500">{{ $txn->tenant_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $typeColors = [
                                'deposit'     => 'bg-green-100 text-green-700',
                                'withdrawal'  => 'bg-red-100 text-red-700',
                                'transfer'    => 'bg-blue-100 text-blue-700',
                                'fee'         => 'bg-slate-100 text-slate-600',
                                'repayment'   => 'bg-purple-100 text-purple-700',
                                'interest'    => 'bg-teal-100 text-teal-700',
                                'disbursement'=> 'bg-orange-100 text-orange-700',
                            ];
                            $tColor = $typeColors[$txn->type] ?? 'bg-slate-100 text-slate-500';
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $tColor }} capitalize">{{ $txn->type }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format($txn->amount, 2) }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = [
                                'success'   => 'bg-green-100 text-green-700',
                                'completed' => 'bg-green-100 text-green-700',
                                'pending'   => 'bg-yellow-100 text-yellow-700',
                                'failed'    => 'bg-red-100 text-red-700',
                            ];
                            $sColor = $statusColors[$txn->status] ?? 'bg-slate-100 text-slate-500';
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $sColor }} capitalize">{{ $txn->status }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-500">
                        {{ \Carbon\Carbon::parse($txn->created_at)->format('d M Y') }}
                        <span class="block text-slate-400">{{ \Carbon\Carbon::parse($txn->created_at)->format('H:i') }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        No transactions found matching your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($transactions->hasPages())
    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

@endsection
