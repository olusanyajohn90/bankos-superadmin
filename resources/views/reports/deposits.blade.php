@extends('layouts.app')
@section('title', 'Deposits Report')
@section('page-title', 'Deposits Report')
@section('page-subtitle', 'Monthly deposit volume by tenant')
@section('content')

<div class="flex items-center justify-between mb-4">
    <a href="/reports" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Reports
    </a>
    <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-200">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500">Tenant</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500">Code</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500">Month</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Transactions</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Total Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($rows as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5 text-sm font-medium text-slate-800">{{ $row->tenant_name }}</td>
                    <td class="px-4 py-2.5 text-xs font-mono text-slate-500">{{ $row->tenant_code }}</td>
                    <td class="px-4 py-2.5 text-xs text-slate-600">{{ $row->month }}</td>
                    <td class="px-4 py-2.5 text-right text-slate-700">{{ number_format($row->txn_count) }}</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-emerald-700">₦{{ number_format($row->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">No data for selected period</td></tr>
                @endforelse
            </tbody>
            @if(count($rows))
            <tfoot>
                <tr class="border-t-2 border-slate-200 bg-slate-50">
                    <td colspan="3" class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Total</td>
                    <td class="px-4 py-3 text-right font-bold text-slate-800">{{ number_format(array_sum(array_column($rows, 'txn_count'))) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-emerald-700">₦{{ number_format(array_sum(array_column($rows, 'total_amount')), 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
