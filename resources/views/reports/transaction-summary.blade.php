@extends('layouts.app')
@section('title', 'Transaction Summary Report')
@section('page-title', 'Transaction Summary')
@section('page-subtitle', 'Volume and count by type and tenant')
@section('content')

<div class="flex items-center justify-between mb-4">
    <a href="/reports" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Reports
    </a>
    <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors">
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
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500">Type</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Count</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Total (₦)</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Avg (₦)</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Max (₦)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($rows as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5">
                        <p class="font-medium text-slate-800">{{ $row->tenant_name }}</p>
                        <p class="text-xs font-mono text-slate-400">{{ $row->tenant_code }}</p>
                    </td>
                    <td class="px-4 py-2.5">
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 capitalize">{{ $row->type }}</span>
                    </td>
                    <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($row->txn_count) }}</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-slate-800">{{ number_format($row->total_amount, 2) }}</td>
                    <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($row->avg_amount, 2) }}</td>
                    <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($row->max_amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">No transactions for selected period</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
