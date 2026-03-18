@extends('layouts.app')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')
@section('page-subtitle', 'All system activity across every tenant')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Total Events', number_format($stats->total), 'bg-blue-50', 'text-blue-600'],
        ['Active Tenants', number_format($stats->tenants_active), 'bg-purple-50', 'text-purple-600'],
        ['Logins', number_format($stats->logins), 'bg-emerald-50', 'text-emerald-600'],
        ['Last 24h', number_format($stats->last_24h), 'bg-amber-50', 'text-amber-600'],
    ] as [$label, $value, $bg, $text])
    <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
        <div class="w-8 h-8 {{ $bg }} rounded-lg flex items-center justify-center mx-auto mb-2">
            <span class="text-xs font-bold {{ $text }}">•</span>
        </div>
        <p class="text-xl font-bold text-slate-900">{{ $value }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ $label }}</p>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="/audit-log" class="bg-white rounded-xl border border-slate-200 p-4 mb-4">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <select name="tenant_id" class="col-span-1 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">All Tenants</option>
            @foreach($tenants as $t)
            <option value="{{ $t->id }}" {{ request('tenant_id') === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
        </select>
        <select name="action" class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">All Actions</option>
            @foreach(['login','logout','create','update','delete','approve','reject','export'] as $act)
            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
            @endforeach
        </select>
        <input type="text" name="entity_type" value="{{ request('entity_type') }}" placeholder="Entity type..."
               class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="date" name="from" value="{{ request('from') }}"
               class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <input type="date" name="to" value="{{ request('to') }}"
               class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Filter</button>
            <a href="/audit-log" class="px-3 py-2 bg-slate-100 text-slate-600 text-sm rounded-lg hover:bg-slate-200 transition-colors">Clear</a>
        </div>
    </div>
</form>

{{-- Log Table --}}
<div class="bg-white rounded-xl border border-slate-200">
    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <p class="text-sm font-semibold text-slate-700">Activity Log <span class="text-slate-400 font-normal">({{ number_format($logs->total()) }} events)</span></p>
        <div class="flex gap-2">
            @foreach($actionCounts as $ac)
            <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $ac->action }}: {{ $ac->cnt }}</span>
            @endforeach
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Time</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Action</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Entity</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">User</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Tenant</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">IP</th>
                    <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2.5 text-xs text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->format('d M H:i') }}</td>
                    <td class="px-4 py-2.5">
                        @php $colors = ['login'=>'bg-green-50 text-green-700','logout'=>'bg-slate-100 text-slate-600','create'=>'bg-blue-50 text-blue-700','update'=>'bg-amber-50 text-amber-700','delete'=>'bg-red-50 text-red-700','approve'=>'bg-emerald-50 text-emerald-700','reject'=>'bg-rose-50 text-rose-700','export'=>'bg-purple-50 text-purple-700']; @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $colors[$log->action] ?? 'bg-slate-100 text-slate-600' }}">{{ $log->action }}</span>
                    </td>
                    <td class="px-4 py-2.5 text-xs font-mono text-slate-700">{{ $log->entity_type }}</td>
                    <td class="px-4 py-2.5 text-xs text-slate-700">{{ $log->user_name }}</td>
                    <td class="px-4 py-2.5 text-xs text-slate-500">{{ $log->tenant_code ?? $log->tenant_name ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-xs font-mono text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                    <td class="px-4 py-2.5 text-xs text-slate-600 max-w-xs truncate">{{ $log->description ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">No audit events found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $logs->links() }}</div>
    @endif
</div>

@endsection
