<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — bankOS SuperAdmin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full bg-slate-50 font-sans antialiased" x-data="{ sidebarOpen: false }">

<div class="flex h-full min-h-screen">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="w-64 flex-shrink-0 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 left-0 z-50">

        {{-- Logo --}}
        <div class="flex items-center gap-3 h-16 px-6 border-b border-slate-200 flex-shrink-0">
            <div class="w-8 h-8 rounded bg-blue-600 grid place-items-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-slate-800">bank<span class="text-blue-600">OS</span></span>
            <span class="ml-auto text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Super</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                <span>Dashboard</span>
            </a>

            <div class="border-t border-slate-200 my-1"></div>

            {{-- Tenants collapsible --}}
            <div x-data="{ open: {{ request()->is('tenants*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors text-slate-600 hover:bg-gray-50">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span class="font-medium text-sm">Tenants</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-200" :class="open ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-0.5 ml-6 pl-3 border-l border-slate-200">
                    <a href="/tenants" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('tenants') || request()->is('tenants?*') && !request()->is('tenants/create') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>All Tenants</span>
                    </a>
                    <a href="/tenants/create" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('tenants/create') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>Add Tenant</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-slate-200 my-1"></div>

            {{-- Analytics collapsible --}}
            <div x-data="{ open: {{ (request()->is('analytics*') || request()->is('customers*') || request()->is('transactions*')) ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors text-slate-600 hover:bg-gray-50">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                        <span class="font-medium text-sm">Analytics</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-200" :class="open ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-0.5 ml-6 pl-3 border-l border-slate-200">
                    <a href="{{ route('analytics.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('analytics') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>Overview</span>
                    </a>
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('customers*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>All Customers</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('transactions*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>Transactions</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-slate-200 my-1"></div>

            {{-- Operations collapsible --}}
            <div x-data="{ open: {{ request()->is('kyc*') || request()->is('loan-applications*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors text-slate-600 hover:bg-gray-50">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span class="font-medium text-sm">Operations</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-200" :class="open ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-0.5 ml-6 pl-3 border-l border-slate-200">
                    <a href="/kyc" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('kyc*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>KYC Queue</span>
                    </a>
                    <a href="/loan-applications" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('loan-applications*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>Loan Applications</span>
                    </a>
                </div>
            </div>

            <div class="border-t border-slate-200 my-1"></div>

            {{-- Compliance collapsible --}}
            <div x-data="{ open: {{ request()->is('audit-log*') || request()->is('system-health*') || request()->is('reports*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors text-slate-600 hover:bg-gray-50">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg>
                        <span class="font-medium text-sm">Compliance</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-200" :class="open ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-0.5 ml-6 pl-3 border-l border-slate-200">
                    <a href="/audit-log" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('audit-log*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>Audit Log</span>
                    </a>
                    <a href="/system-health" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('system-health*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>System Health</span>
                    </a>
                    <a href="/reports" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->is('reports*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-gray-50' }}">
                        <span>Regulatory Reports</span>
                    </a>
                </div>
            </div>

        </nav>

        {{-- Admin info at bottom --}}
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold uppercase">{{ substr(auth('superadmin')->user()->name ?? 'SA', 0, 2) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-slate-800 text-xs font-semibold truncate">{{ auth('superadmin')->user()->name ?? '' }}</p>
                    <p class="text-slate-400 text-[10px] truncate uppercase tracking-wide">{{ auth('superadmin')->user()->role ?? 'superadmin' }}</p>
                </div>
            </div>
        </div>

    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="flex-1 flex flex-col ml-64 min-h-screen">

        {{-- Top bar --}}
        <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center gap-4 sticky top-0 z-40">
            <div class="flex-shrink-0">
                <h1 class="text-base font-bold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-slate-400 mt-0.5">@yield('page-subtitle', 'Platform-wide overview')</p>
            </div>
            {{-- Global Search --}}
            <form method="GET" action="{{ route('search') }}" class="flex-1 max-w-md hidden md:block">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-400"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Search tenants, customers, accounts..."
                           class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white">
                </div>
            </form>
            <div class="flex items-center gap-3 ml-auto flex-shrink-0">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-slate-700">{{ auth('superadmin')->user()->name ?? '' }}</p>
                    <p class="text-xs text-slate-400">{{ now()->format('D, d M Y') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-600 text-xs font-medium rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mx-6 mt-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm flex items-start gap-2">
            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 px-6 py-5">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="px-6 py-3 border-t border-slate-200 bg-white">
            <p class="text-xs text-slate-400 text-center">bankOS SuperAdmin Console &mdash; &copy; {{ date('Y') }} Antigravity Technologies</p>
        </footer>

    </div>
</div>

@stack('scripts')
</body>
</html>
