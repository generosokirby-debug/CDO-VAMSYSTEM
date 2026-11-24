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
    <aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-40 flex w-[290px] -translate-x-full flex-col overflow-y-auto rounded-r-[32px] border-r border-slate-200 bg-white text-slate-800 shadow-2xl transition-transform duration-200 lg:translate-x-0">
        <a href="{{ route('dashboard') }}" class="flex h-[170px] shrink-0 items-center gap-4 px-8">
            <span class="flex h-[68px] w-[68px] shrink-0 items-center justify-center rounded-[18px] bg-[#182746] text-white shadow-lg shadow-slate-300">
                <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 17h12M7 17l1-8h8l1 8M5 20h14M8 9V7a4 4 0 0 1 8 0v2M9 13h6"/>
                </svg>
            </span>
            <span class="flex flex-col">
                <span class="text-[24px] font-bold leading-none tracking-tight text-slate-900">CDO VAMS</span>
                <span class="mt-3 text-[16px] font-medium text-slate-500">Administrator Panel</span>
            </span>
        </a>

        <div class="mx-8 shrink-0 border-t border-slate-200"></div>

        <div class="px-8 pt-9">
            <p class="mb-5 text-[13px] font-bold uppercase tracking-[0.02em] text-slate-400">Main Menu</p>
            <nav class="flex flex-col gap-3">
                @if (auth()->user()->hasModuleAccess('dashboard'))
                <a href="{{ route('dashboard') }}" @class([
                    'flex min-h-14 items-center gap-4 rounded-xl px-2 text-[16px] font-semibold transition',
                    'bg-slate-100 text-slate-950' => request()->routeIs('dashboard'),
                    'text-slate-700 hover:text-slate-950' => ! request()->routeIs('dashboard'),
                ])>
                    <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    Dashboard
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('employees'))
                <a href="{{ route('employees.index') }}" @class([
                    'flex min-h-14 items-center gap-4 rounded-xl px-2 text-[16px] font-semibold transition',
                    'bg-slate-100 text-slate-950' => request()->routeIs('employees.*'),
                    'text-slate-700 hover:text-slate-950' => ! request()->routeIs('employees.*'),
                ])>
                    <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="9" cy="7" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 20v-2a5 5 0 0 1 5-5h2a5 5 0 0 1 5 5v2M15 14a4 4 0 0 1 6 3v3"/>
                    </svg>
                    Employees
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('vehicles'))
                <a href="{{ route('vehicles.index') }}" @class([
                    'flex min-h-14 items-center gap-4 rounded-xl px-2 text-[16px] font-semibold transition',
                    'bg-slate-100 text-slate-950' => request()->routeIs('vehicles.*'),
                    'text-slate-700 hover:text-slate-950' => ! request()->routeIs('vehicles.*'),
                ])>
                    <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 17h14l-1-7H6l-1 7Zm2-7 2-4h6l2 4M7 17v2m10-2v2M9 13h6"/>
                    </svg>
                    Vehicles &amp; QR Passes
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('gate-scanner'))
                <a href="{{ route('gate-scanner.index') }}" @class([
                    'flex min-h-14 items-center gap-4 rounded-xl px-2 text-[16px] font-semibold transition',
                    'bg-slate-100 text-slate-950' => request()->routeIs('gate-scanner.*'),
                    'text-slate-700 hover:text-slate-950' => ! request()->routeIs('gate-scanner.*'),
                ])>
                    <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 3H5a2 2 0 0 0-2 2v3m13-5h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3m13 5h3a2 2 0 0 0 2-2v-3M8 8h8v8H8z"/>
                    </svg>
                    Gate Scanner
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('logs'))
                <a href="{{ route('logs.index') }}" @class([
                    'flex min-h-14 items-center gap-4 rounded-xl px-2 text-[16px] font-semibold transition',
                    'bg-slate-100 text-slate-950' => request()->routeIs('logs.*'),
                    'text-slate-700 hover:text-slate-950' => ! request()->routeIs('logs.*'),
                ])>
                    <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="5" y="4" width="14" height="17" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 2v4m6-4v4M9 10h6M9 14h6M9 18h4"/>
                    </svg>
                    Logs &amp; Reports
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('my-vehicles'))
                <a href="{{ route('my-vehicles.index') }}" @class([
                    'flex min-h-14 items-center gap-4 rounded-xl px-2 text-[16px] font-semibold transition',
                    'bg-slate-100 text-slate-950' => request()->routeIs('my-vehicles.*'),
                    'text-slate-700 hover:text-slate-950' => ! request()->routeIs('my-vehicles.*'),
                ])>
                    <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16l2-5h14l2 5v4h-2v-2H5v2H3v-4Zm3-5 2-4h8l2 4M7 14h.01M17 14h.01"/>
                    </svg>
                    My Vehicles
                </a>
                @endif
            </nav>
        </div>

        @if (auth()->user()->hasModuleAccess('settings'))
        <div class="mx-8 mt-8 shrink-0 border-t border-slate-200"></div>

        <div class="px-8 pt-8">
            <p class="mb-4 text-[13px] font-bold uppercase tracking-[0.02em] text-slate-400">Administration</p>
            <a href="{{ route('settings.index') }}" @class([
                'flex min-h-14 items-center gap-4 rounded-xl px-2 text-[16px] font-semibold transition',
                'bg-slate-100 text-slate-950' => request()->routeIs('settings.*'),
                'text-slate-700 hover:text-slate-950' => ! request()->routeIs('settings.*'),
            ])>
                <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.42 1.42-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.04 1.56V19.6h-2v-.08a1.7 1.7 0 0 0-1.04-1.56 1.7 1.7 0 0 0-1.88.34l-.06.06-1.42-1.42.06-.06A1.7 1.7 0 0 0 9.3 15a1.7 1.7 0 0 0-1.56-1.04H7.6v-2h.14A1.7 1.7 0 0 0 9.3 10.92a1.7 1.7 0 0 0-.34-1.88l-.06-.06L10.32 7.56l.06.06A1.7 1.7 0 0 0 12.26 7.28 1.7 1.7 0 0 0 13.3 5.72v-.14h2v.14a1.7 1.7 0 0 0 1.04 1.56 1.7 1.7 0 0 0 1.88-.34l.06-.06L19.7 8.3l-.06.06A1.7 1.7 0 0 0 19.3 10.24a1.7 1.7 0 0 0 1.56 1.04h.14v2h-.14A1.7 1.7 0 0 0 19.4 15Z"/>
                </svg>
                Settings &amp; Permissions
            </a>
        </div>
        @endif

        <div class="mt-auto px-8 pb-8 pt-10">
            @auth
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 21a8 8 0 0 1 16 0"/>
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        @if (auth()->user()->hasAdminAccess())
                            <a href="{{ route('profile.show') }}" class="block truncate text-[16px] font-bold text-slate-900 hover:text-slate-700">{{ auth()->user()->name }}</a>
                        @else
                            <p class="truncate text-[16px] font-bold text-slate-900">{{ auth()->user()->name }}</p>
                        @endif
                        <p class="mt-1 text-sm font-medium text-slate-500">{{ auth()->user()->role }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-slate-500 transition hover:text-slate-900">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 21a8 8 0 0 1 16 0"/>
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                    </span>
                    <span class="flex flex-col">
                        <span class="text-[16px] font-bold text-slate-900">Administrator</span>
                        <span class="mt-1 text-sm font-medium text-slate-500">Login</span>
                    </span>
                </a>
            @endauth
        </div>
    </aside>

    <div class="min-h-screen lg:ml-[290px]">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8">
            <div class="flex items-center gap-3">
                <button id="sidebar-toggle" type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="Open navigation" aria-expanded="false">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="text-sm font-medium text-slate-500">CDO VAMS</span>
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
