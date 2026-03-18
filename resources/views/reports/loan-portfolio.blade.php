@extends('layouts.app')
@section('title', 'Loan Portfolio Report')
@section('page-title', 'Loan Portfolio Report')
@section('page-subtitle', 'Outstanding balances, PAR and NPL by tenant')
@section('content')

<div class="flex items-center justify-between mb-4">
    <a href="/reports" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Reports
    </a>
    <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
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
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Total</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Active</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Overdue</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Defaulted</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Outstanding</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">PAR %</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">NPL %</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($rows as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5">
                        <p class="font-medium text-slate-800">{{ $row->tenant_name }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ $row->tenant_code }}</p>
                    </td>
                    <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($row->total_loans) }}</td>
                    <td class="px-4 py-2.5 text-right text-green-600">{{ number_format($row->active_loans) }}</td>
                    <td class="px-4 py-2.5 text-right text-amber-600">{{ number_format($row->overdue_loans) }}</td>
                    <td class="px-4 py-2.5 text-right text-red-600">{{ number_format($row->defaulted_loans) }}</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-slate-800">₦{{ number_format($row->total_outstanding, 2) }}</td>
                    <td class="px-4 py-2.5 text-right">
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded {{ $row->par_ratio > 10 ? 'bg-red-50 text-red-600' : ($row->par_ratio > 5 ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600') }}">{{ $row->par_ratio }}%</span>
                    </td>
                    <td class="px-4 py-2.5 text-right">
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded {{ $row->npl_ratio > 5 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">{{ $row->npl_ratio }}%</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-slate-400">No loan data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
