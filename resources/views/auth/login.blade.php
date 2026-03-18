<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — bankOS SuperAdmin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-100">

<div class="min-h-screen flex">

    {{-- Left panel — dark brand --}}
    <div class="hidden lg:flex lg:w-1/2 bg-[#0f172a] flex-col items-center justify-center relative overflow-hidden px-16">

        {{-- Background decoration --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-900/20 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 text-center">
            {{-- Logo mark --}}
            <div class="flex items-center justify-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-blue-900/50">
                    <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-4xl font-black text-white tracking-tight mb-2">bankOS</h1>
            <div class="inline-flex items-center gap-2 bg-blue-600/20 border border-blue-500/30 rounded-full px-4 py-1.5 mb-8">
                <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                <span class="text-blue-300 text-sm font-semibold uppercase tracking-widest">SuperAdmin Console</span>
            </div>

            <p class="text-slate-400 text-base max-w-sm mx-auto leading-relaxed">
                Platform-level administration for the bankOS multi-tenant banking infrastructure.
            </p>

            <div class="mt-12 grid grid-cols-3 gap-6 text-center">
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <p class="text-2xl font-bold text-white">MFBs</p>
                    <p class="text-xs text-slate-500 mt-1">Microfinance Banks</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <p class="text-2xl font-bold text-white">Co-ops</p>
                    <p class="text-xs text-slate-500 mt-1">Cooperatives</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                    <p class="text-2xl font-bold text-white">Lenders</p>
                    <p class="text-xs text-slate-500 mt-1">Digital Lenders</p>
                </div>
            </div>
        </div>

        {{-- Bottom badge --}}
        <div class="absolute bottom-8 left-0 right-0 text-center">
            <p class="text-slate-600 text-xs">Restricted Access &mdash; Authorised Personnel Only</p>
        </div>
    </div>

    {{-- Right panel — login form --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12 bg-white">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
                <div>
                    <p class="font-black text-slate-900 text-xl">bankOS</p>
                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">SuperAdmin Console</span>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Welcome back</h2>
                <p class="text-slate-500 mt-2 text-sm">Sign in to the SuperAdmin console to manage the platform.</p>
            </div>

            {{-- Error messages --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="text-red-700 text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@bankos.io"
                            autocomplete="email"
                            required
                            class="w-full pl-10 pr-4 py-3 border @error('email') border-red-400 bg-red-50 @else border-slate-300 bg-white @enderror rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••••••"
                            autocomplete="current-password"
                            required
                            class="w-full pl-10 pr-4 py-3 border @error('password') border-red-400 bg-red-50 @else border-slate-300 bg-white @enderror rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-slate-600">Keep me signed in</label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-150 text-sm shadow-lg shadow-blue-600/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Sign in to Console
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-slate-400">
                Protected by bankOS platform security. Unauthorised access is prohibited.
            </p>

        </div>
    </div>

</div>

</body>
</html>
