<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | CDO Staff Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-950/50 lg:hidden"></div>
    <aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-[#142239] text-white shadow-xl transition-transform duration-200 lg:translate-x-0">
        <a href="{{ route('dashboard') }}" class="flex h-24 items-center gap-3 border-b border-white/10 px-7">
            <img src="{{ asset('cdo-logo.png') }}" alt="CDO logo" class="h-14 w-14 rounded-xl bg-white object-contain p-1">
            <span class="flex flex-col">
                <span class="text-sm font-bold tracking-wide">CDO PORTAL</span>
                <span class="mt-1 text-xs text-slate-400">Staff workspace</span>
            </span>
        </a>

        <div class="px-5 pt-8">
            <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">Workspace</p>
            <nav class="flex flex-col gap-1">
                <a href="{{ route('dashboard') }}" @class([
                    'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition',
                    'bg-blue-500 text-white shadow-lg shadow-blue-950/30' => request()->routeIs('dashboard'),
                    'text-slate-300 hover:bg-white/10 hover:text-white' => ! request()->routeIs('dashboard'),
                ])>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5M5.5 9v11h13V9M9 20v-6h6v6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('dashboard') }}#records" @class([
                    'flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition',
                    'bg-blue-500 text-white shadow-lg shadow-blue-950/30' => request()->routeIs('cdos.*'),
                    'text-slate-300 hover:bg-white/10 hover:text-white' => ! request()->routeIs('cdos.*'),
                ])>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                    CDO Records
                </a>
            </nav>
        </div>

        <div class="mt-auto border-t border-white/10 p-5">
            <div class="rounded-xl bg-white/5 p-4">
                <p class="text-xs font-semibold text-slate-200">Testing workspace</p>
                <p class="mt-1 text-xs leading-5 text-slate-400">Use sample information only. Do not enter real personal data.</p>
            </div>
            @auth
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5m5 5H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                        </svg>
                        Sign out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mt-4 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 8l4 4-4 4m4-4H3m9-9h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/>
                    </svg>
                    Sign in
                </a>
            @endauth
        </div>
    </aside>

    <div class="min-h-screen lg:ml-64">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8">
            <div class="flex items-center gap-3">
                <button id="sidebar-toggle" type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="Open navigation" aria-expanded="false">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="text-sm font-medium text-slate-500">CDO Staff Portal</span>
                <span class="hidden text-slate-300 sm:inline">/</span>
                <span class="hidden text-sm font-semibold text-slate-800 sm:inline">@yield('breadcrumb', 'Dashboard')</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-amber-700 sm:inline-flex">Test mode</span>
                @auth
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden text-sm font-semibold text-slate-700 sm:inline">{{ auth()->user()->name }}</span>
                @else
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">D</div>
                    <span class="hidden text-sm font-semibold text-slate-700 sm:inline">Demo visitor</span>
                @endauth
            </div>
        </header>

        <main class="mx-auto w-full max-w-[1440px] px-4 py-7 sm:px-8 sm:py-9">
            @yield('content')
        </main>
    </div>
</body>
</html>
