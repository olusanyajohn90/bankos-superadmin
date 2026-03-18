@extends('layouts.app')

@section('title', 'Edit — ' . $tenant->name)
@section('page-title', 'Edit Tenant')
@section('page-subtitle', 'Update settings for ' . $tenant->name)

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-slate-400 mb-5">
    <a href="{{ route('tenants.index') }}" class="hover:text-slate-700 transition-colors">Tenants</a>
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('tenants.show', $tenant->id) }}" class="hover:text-slate-700 transition-colors">{{ $tenant->name }}</a>
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-slate-600 font-medium">Edit</span>
</nav>

@if($errors->any())
<div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-5 py-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="font-semibold text-red-800 text-sm mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                <li class="text-sm text-red-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

@php
    $address = '';
    if ($tenant->address) {
        $decoded = json_decode($tenant->address, true);
        $address = is_array($decoded) ? ($decoded['line1'] ?? '') : $tenant->address;
    }
@endphp

<form method="POST" action="{{ route('tenants.update', $tenant->id) }}">
@csrf
@method('PUT')

<div class="space-y-6 max-w-3xl">

    {{-- Section 1: Institution Details --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-3">
            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white text-xs font-bold">1</span>
            </div>
            <div>
                <h2 class="font-semibold text-slate-900">Institution Details</h2>
                <p class="text-xs text-slate-500">Core identity and classification</p>
            </div>
        </div>
        <div class="px-6 py-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Institution Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                    placeholder="e.g. Sunrise Microfinance Bank"
                    class="w-full border {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-slate-200' }} rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    oninput="updatePreview()">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Institution Code <span class="text-red-500">*</span>
                    <span class="text-xs font-normal text-slate-400 ml-1">(Staff login code)</span>
                </label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $tenant->short_name) }}" required maxlength="20"
                    class="w-full border {{ $errors->has('short_name') ? 'border-red-400 bg-red-50' : 'border-slate-200' }} rounded-xl px-4 py-2.5 text-sm text-slate-700 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g,''); updatePreview()">
                @error('short_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Institution Type <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full border {{ $errors->has('type') ? 'border-red-400 bg-red-50' : 'border-slate-200' }} rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    <option value="bank"           {{ old('type', $tenant->type) === 'bank'           ? 'selected' : '' }}>Bank (MFB)</option>
                    <option value="cooperative"    {{ old('type', $tenant->type) === 'cooperative'    ? 'selected' : '' }}>Cooperative</option>
                    <option value="digital_lender" {{ old('type', $tenant->type) === 'digital_lender' ? 'selected' : '' }}>Digital Lender</option>
                    <option value="microfinance"   {{ old('type', $tenant->type) === 'microfinance'   ? 'selected' : '' }}>Microfinance</option>
                </select>
                @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Subscription Plan <span class="text-red-500">*</span></label>
                <select name="subscription_plan" required class="w-full border {{ $errors->has('subscription_plan') ? 'border-red-400 bg-red-50' : 'border-slate-200' }} rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                    <option value="starter"    {{ old('subscription_plan', $tenant->subscription_plan) === 'starter'    ? 'selected' : '' }}>Starter</option>
                    <option value="growth"     {{ old('subscription_plan', $tenant->subscription_plan) === 'growth'     ? 'selected' : '' }}>Growth</option>
                    <option value="enterprise" {{ old('subscription_plan', $tenant->subscription_plan) === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
                @error('subscription_plan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Domain <span class="text-xs font-normal text-slate-400 ml-1">(optional)</span></label>
                <input type="text" name="domain" value="{{ old('domain', $tenant->domain) }}" placeholder="e.g. sunrise.bankos.ng"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('domain')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

        </div>
    </div>

    {{-- Section 2: Contact & Compliance --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-3">
            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white text-xs font-bold">2</span>
            </div>
            <div>
                <h2 class="font-semibold text-slate-900">Contact &amp; Compliance</h2>
                <p class="text-xs text-slate-500">Regulatory information and contact details</p>
            </div>
        </div>
        <div class="px-6 py-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Contact Email</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $tenant->contact_email) }}" placeholder="admin@institution.com"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('contact_email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Contact Phone</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $tenant->contact_phone) }}" placeholder="+2348012345678"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('contact_phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">CBN License Number</label>
                <input type="text" name="cbn_license_number" value="{{ old('cbn_license_number', $tenant->cbn_license_number) }}" placeholder="CBN/MFB/2024/001"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('cbn_license_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIBSS Institution Code</label>
                <input type="text" name="nibss_institution_code" value="{{ old('nibss_institution_code', $tenant->nibss_institution_code) }}" placeholder="e.g. 090XXX"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('nibss_institution_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Address</label>
                <input type="text" name="address" value="{{ old('address', $address) }}" placeholder="123 Bank Street, Victoria Island, Lagos"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

        </div>
    </div>

    {{-- Section 3: Branding --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-3">
            <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white text-xs font-bold">3</span>
            </div>
            <div>
                <h2 class="font-semibold text-slate-900">Branding</h2>
                <p class="text-xs text-slate-500">Brand colors for the tenant portal</p>
            </div>
        </div>
        <div class="px-6 py-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Primary Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $tenant->primary_color ?? '#2563eb') }}"
                        class="w-12 h-10 rounded-lg border border-slate-200 cursor-pointer p-0.5"
                        oninput="document.getElementById('primary_color_text').value = this.value; updatePreview()">
                    <input type="text" id="primary_color_text" value="{{ old('primary_color', $tenant->primary_color ?? '#2563eb') }}" maxlength="7"
                        class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){document.getElementById('primary_color').value=this.value; updatePreview()}"
                        onchange="document.querySelector('input[name=primary_color]').value = this.value">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Secondary Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $tenant->secondary_color ?? '#0c2461') }}"
                        class="w-12 h-10 rounded-lg border border-slate-200 cursor-pointer p-0.5"
                        oninput="document.getElementById('secondary_color_text').value = this.value; updatePreview()">
                    <input type="text" id="secondary_color_text" value="{{ old('secondary_color', $tenant->secondary_color ?? '#0c2461') }}" maxlength="7"
                        class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){document.getElementById('secondary_color').value=this.value; updatePreview()}"
                        onchange="document.querySelector('input[name=secondary_color]').value = this.value">
                </div>
            </div>

            {{-- Color preview --}}
            <div class="sm:col-span-2">
                <div id="colorPreview" class="h-14 rounded-xl flex items-center px-5 gap-3 transition-all duration-300"
                    style="background: linear-gradient(135deg, {{ $tenant->primary_color ?? '#2563eb' }}, {{ $tenant->secondary_color ?? '#0c2461' }})">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                        <span class="text-white text-xs font-bold" id="previewInitials">{{ strtoupper(substr($tenant->short_name, 0, 2)) }}</span>
                    </div>
                    <span class="text-white font-semibold text-sm" id="previewName">{{ $tenant->name }}</span>
                </div>
                <p class="text-xs text-slate-400 mt-1.5">Brand color preview</p>
            </div>

        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center justify-between pb-4">
        <a href="{{ route('tenants.show', $tenant->id) }}" class="text-sm text-slate-500 hover:text-slate-700 transition-colors">
            &larr; Back to tenant
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('tenants.show', $tenant->id) }}" class="px-6 py-2.5 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save Changes
            </button>
        </div>
    </div>

</div>
</form>

<script>
function updatePreview() {
    const primary = document.getElementById('primary_color').value || '#2563eb';
    const secondary = document.getElementById('secondary_color').value || '#0c2461';
    const nameInput = document.querySelector('input[name="name"]');
    const codeInput = document.getElementById('short_name');
    const name = nameInput ? (nameInput.value || 'Institution Name') : 'Institution Name';
    const code = codeInput ? (codeInput.value || '??').substring(0, 2).toUpperCase() : '??';
    document.getElementById('colorPreview').style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewInitials').textContent = code || '??';
}

document.querySelector('input[name="name"]').addEventListener('input', updatePreview);
</script>

@endsection
