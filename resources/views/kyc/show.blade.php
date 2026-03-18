@extends('layouts.app')

@section('title', 'KYC Review')
@section('page-title', 'KYC Review')
@section('page-subtitle', 'Customer identity verification details')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Customer Info --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold text-lg uppercase">{{ substr($customer->first_name,0,1) }}{{ substr($customer->last_name,0,1) }}</span>
                </div>
                <div>
                    <p class="font-bold text-slate-900">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $customer->customer_number }}</p>
                </div>
            </div>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Tenant</span><span class="font-medium text-slate-800">{{ $customer->tenant_name }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Phone</span><span class="font-medium text-slate-800">{{ $customer->phone }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Email</span><span class="font-medium text-slate-800 text-xs">{{ $customer->email }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">KYC Tier</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium">{{ str_replace('level_', 'Level ', $customer->kyc_tier) }}</span>
                </div>
                <div class="flex justify-between"><span class="text-slate-500">KYC Status</span>
                    @php $statusColors = ['manual_review'=>'bg-amber-50 text-amber-700','approved'=>'bg-green-50 text-green-700','rejected'=>'bg-red-50 text-red-700','auto_approved'=>'bg-emerald-50 text-emerald-700']; @endphp
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statusColors[$customer->kyc_status] ?? 'bg-slate-100 text-slate-600' }}">{{ str_replace('_',' ',ucfirst($customer->kyc_status)) }}</span>
                </div>
                <div class="flex justify-between"><span class="text-slate-500">Account Status</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $customer->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $customer->status }}</span>
                </div>
                <div class="flex justify-between"><span class="text-slate-500">Registered</span><span class="text-xs text-slate-600">{{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y') }}</span></div>
            </div>
        </div>

        {{-- Accounts --}}
        @if($accounts->count())
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Accounts ({{ $accounts->count() }})</p>
            <div class="space-y-2">
                @foreach($accounts as $acc)
                <div class="flex items-center justify-between text-xs">
                    <div>
                        <p class="font-mono font-medium text-slate-800">{{ $acc->account_number }}</p>
                        <p class="text-slate-400">{{ ucfirst($acc->type) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-slate-800">₦{{ number_format($acc->available_balance, 2) }}</p>
                        <span class="{{ $acc->status === 'active' ? 'text-green-600' : 'text-slate-400' }}">{{ $acc->status }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <a href="/kyc" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Back to KYC Queue
        </a>
    </div>

    {{-- Documents --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-3 border-b border-slate-100">
                <p class="text-sm font-semibold text-slate-700">KYC Documents ({{ $documents->count() }})</p>
            </div>
            @forelse($documents as $doc)
            <div class="px-5 py-4 border-b border-slate-50 last:border-b-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-semibold text-slate-800">{{ ucwords(str_replace('_',' ',$doc->document_type)) }}</span>
                            @php $docColors = ['pending'=>'bg-amber-50 text-amber-700','approved'=>'bg-green-50 text-green-700','rejected'=>'bg-red-50 text-red-700','expired'=>'bg-slate-100 text-slate-600']; @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $docColors[$doc->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $doc->status }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-slate-500">
                            @if($doc->document_number)
                            <div><span class="text-slate-400">Doc No:</span> <span class="font-mono text-slate-700">{{ $doc->document_number }}</span></div>
                            @endif
                            @if($doc->expiry_date)
                            <div><span class="text-slate-400">Expires:</span> <span class="text-slate-700">{{ \Carbon\Carbon::parse($doc->expiry_date)->format('d M Y') }}</span></div>
                            @endif
                            <div><span class="text-slate-400">Submitted:</span> <span class="text-slate-700">{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y H:i') }}</span></div>
                            @if($doc->review_notes)
                            <div class="col-span-2"><span class="text-slate-400">Notes:</span> <span class="text-slate-700">{{ $doc->review_notes }}</span></div>
                            @endif
                        </div>
                    </div>
                    @if($doc->file_path)
                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                       class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 border border-slate-200 text-slate-600 text-xs rounded-lg hover:bg-slate-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        View File
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <p class="text-sm text-slate-400">No documents submitted yet</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
