@extends('layouts.app')
@section('title', 'Customer Growth Report')
@section('page-title', 'Customer Growth Report')
@section('page-subtitle', 'Monthly new registrations and KYC rates')
@section('content')

<div class="flex items-center justify-between mb-4">
    <a href="/reports" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Reports
    </a>
    <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
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
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500">Month</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">New Customers</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">KYC Approved</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Pending KYC</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500">Approval Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($rows as $row)
                @php $rate = $row->new_customers > 0 ? round(($row->approved / $row->new_customers)*100,1) : 0; @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5">
                        <p class="font-medium text-slate-800">{{ $row->tenant_name }}</p>
                        <p class="text-xs font-mono text-slate-400">{{ $row->tenant_code }}</p>
                    </td>
                    <td class="px-4 py-2.5 text-xs text-slate-600">{{ $row->month }}</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-slate-800">{{ number_format($row->new_customers) }}</td>
                    <td class="px-4 py-2.5 text-right text-green-600">{{ number_format($row->approved) }}</td>
                    <td class="px-4 py-2.5 text-right text-amber-600">{{ number_format($row->pending_kyc) }}</td>
                    <td class="px-4 py-2.5 text-right">
                        <span class="text-xs font-semibold px-1.5 py-0.5 rounded {{ $rate >= 80 ? 'bg-green-50 text-green-700' : ($rate >= 50 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">{{ $rate }}%</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">No data for selected period</td></tr>
                @endforelse
            </tbody>
            @if(count($rows))
            <tfoot>
                <tr class="border-t-2 border-slate-200 bg-slate-50">
                    <td colspan="2" class="px-4 py-3 text-xs font-semibold text-slate-600 uppercase">Total</td>
                    <td class="px-4 py-3 text-right font-bold text-slate-800">{{ number_format(array_sum(array_column($rows,'new_customers'))) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-green-700">{{ number_format(array_sum(array_column($rows,'approved'))) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-amber-700">{{ number_format(array_sum(array_column($rows,'pending_kyc'))) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
