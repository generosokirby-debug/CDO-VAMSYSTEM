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
    <aside id="dashboard-sidebar" class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col overflow-hidden rounded-r-2xl border-r border-slate-200 bg-white text-slate-800 shadow-xl transition-transform duration-200 lg:translate-x-0">
        <a href="{{ route('dashboard') }}" class="flex h-12 shrink-0 items-center gap-3 px-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#172b3a] text-white shadow-sm">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 17h12M7 17l1-8h8l1 8M5 20h14M8 9V7a4 4 0 0 1 8 0v2M9 13h6"/>
                </svg>
            </span>
            <span class="flex flex-col">
                <span class="text-lg font-bold leading-tight tracking-tight text-slate-900">CDO VAMS</span>
                <span class="text-xs font-medium text-slate-500">Administrator Panel</span>
            </span>
        </a>

        <div class="mx-4 shrink-0 border-t border-slate-200"></div>

        <div class="px-4 pt-2">
            <p class="mb-1 text-[10px] font-bold uppercase leading-3 tracking-[0.12em] text-slate-400">Main Menu</p>
            <nav class="flex flex-col gap-1">
                @if (auth()->user()->hasModuleAccess('dashboard'))
                <a href="{{ route('dashboard') }}" @class([
                    'flex min-h-8 items-center gap-3 rounded-lg px-2.5 text-sm font-semibold transition-colors',
                    'bg-[#f2b84b] text-[#172b3a] shadow-sm' => request()->routeIs('dashboard'),
                    'text-slate-600 hover:bg-slate-100 hover:text-[#172b3a]' => ! request()->routeIs('dashboard'),
                ])>
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
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
                    'flex min-h-8 items-center gap-3 rounded-lg px-2.5 text-sm font-semibold transition-colors',
                    'bg-[#f2b84b] text-[#172b3a] shadow-sm' => request()->routeIs('employees.*'),
                    'text-slate-600 hover:bg-slate-100 hover:text-[#172b3a]' => ! request()->routeIs('employees.*'),
                ])>
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="9" cy="7" r="3"/>
                        <circle cx="17" cy="9" r="2.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 20v-2a5 5 0 0 1 5-5h2a5 5 0 0 1 5 5v2M15 14a4 4 0 0 1 6 3v3"/>
                    </svg>
                    Employees
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('vehicles'))
                <a href="{{ route('vehicles.index') }}" @class([
                    'flex min-h-8 items-center gap-3 rounded-lg px-2.5 text-sm font-semibold transition-colors',
                    'bg-[#f2b84b] text-[#172b3a] shadow-sm' => request()->routeIs('vehicles.*'),
                    'text-slate-600 hover:bg-slate-100 hover:text-[#172b3a]' => ! request()->routeIs('vehicles.*'),
                ])>
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 17h14l-1-7H6l-1 7Zm2-7 2-4h6l2 4M7 17v2m10-2v2M9 13h6"/>
                    </svg>
                    Vehicles &amp; QR Passes
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('gate-scanner'))
                <a href="{{ route('gate-scanner.index') }}" @class([
                    'flex min-h-8 items-center gap-3 rounded-lg px-2.5 text-sm font-semibold transition-colors',
                    'bg-[#f2b84b] text-[#172b3a] shadow-sm' => request()->routeIs('gate-scanner.*'),
                    'text-slate-600 hover:bg-slate-100 hover:text-[#172b3a]' => ! request()->routeIs('gate-scanner.*'),
                ])>
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 3H5a2 2 0 0 0-2 2v3m13-5h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3m13 5h3a2 2 0 0 0 2-2v-3M8 8h8v8H8z"/>
                    </svg>
                    Gate Scanner
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('logs'))
                <a href="{{ route('logs.index') }}" @class([
                    'flex min-h-8 items-center gap-3 rounded-lg px-2.5 text-sm font-semibold transition-colors',
                    'bg-[#f2b84b] text-[#172b3a] shadow-sm' => request()->routeIs('logs.*'),
                    'text-slate-600 hover:bg-slate-100 hover:text-[#172b3a]' => ! request()->routeIs('logs.*'),
                ])>
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="5" y="4" width="14" height="17" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 2v4m6-4v4M9 10h6M9 14h6M9 18h4"/>
                    </svg>
                    Logs &amp; Reports
                </a>
                @endif

                @if (auth()->user()->hasModuleAccess('my-vehicles'))
                <a href="{{ route('my-vehicles.index') }}" @class([
                    'flex min-h-8 items-center gap-3 rounded-lg px-2.5 text-sm font-semibold transition-colors',
                    'bg-[#f2b84b] text-[#172b3a] shadow-sm' => request()->routeIs('my-vehicles.*'),
                    'text-slate-600 hover:bg-slate-100 hover:text-[#172b3a]' => ! request()->routeIs('my-vehicles.*'),
                ])>
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16l2-5h14l2 5v4h-2v-2H5v2H3v-4Zm3-5 2-4h8l2 4M7 14h.01M17 14h.01"/>
                    </svg>
                    My Vehicles
                </a>
                @endif
            </nav>
        </div>

        @if (auth()->user()->hasModuleAccess('settings'))
        <div class="mx-4 mt-2 shrink-0 border-t border-slate-200"></div>

        <div class="px-4 pt-2">
            <p class="mb-1 text-[10px] font-bold uppercase leading-3 tracking-[0.12em] text-slate-400">Administration</p>
            <a href="{{ route('settings.index') }}" @class([
                'flex min-h-8 items-center gap-3 rounded-lg px-2.5 text-sm font-semibold transition-colors',
                'bg-[#f2b84b] text-[#172b3a] shadow-sm' => request()->routeIs('settings.*'),
                'text-slate-600 hover:bg-slate-100 hover:text-[#172b3a]' => ! request()->routeIs('settings.*'),
            ])>
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="3"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.42 1.42-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.04 1.56V19.6h-2v-.08a1.7 1.7 0 0 0-1.04-1.56 1.7 1.7 0 0 0-1.88.34l-.06.06-1.42-1.42.06-.06A1.7 1.7 0 0 0 9.3 15a1.7 1.7 0 0 0-1.56-1.04H7.6v-2h.14A1.7 1.7 0 0 0 9.3 10.92a1.7 1.7 0 0 0-.34-1.88l-.06-.06L10.32 7.56l.06.06A1.7 1.7 0 0 0 12.26 7.28 1.7 1.7 0 0 0 13.3 5.72v-.14h2v.14a1.7 1.7 0 0 0 1.04 1.56 1.7 1.7 0 0 0 1.88-.34l.06-.06L19.7 8.3l-.06.06A1.7 1.7 0 0 0 19.3 10.24a1.7 1.7 0 0 0 1.56 1.04h.14v2h-.14A1.7 1.7 0 0 0 19.4 15Z"/>
                </svg>
                Settings &amp; Permissions
            </a>
        </div>
        @endif

        <div class="mt-auto px-4 py-2">
            @auth
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#172b3a] text-[#f2b84b]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 21a8 8 0 0 1 16 0"/>
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        @if (auth()->user()->hasAdminAccess())
                            <a href="{{ route('profile.show') }}" class="block truncate text-[13px] font-bold leading-4 text-slate-900 hover:text-slate-700">{{ auth()->user()->name }}</a>
                        @else
                            <p class="truncate text-[13px] font-bold leading-4 text-slate-900">{{ auth()->user()->name }}</p>
                        @endif
                        <div class="mt-0.5 flex items-center justify-between gap-2">
                            <p class="text-[10px] font-medium leading-3 text-slate-500">{{ auth()->user()->role }}</p>
                            <form method="POST" action="{{ route('logout') }}" class="inline-flex" data-alert-action="logout">
                            @csrf
                                <button type="submit" class="text-[10px] font-semibold leading-3 text-slate-500 transition hover:text-slate-900">Logout</button>
                            </form>
                        </div>
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

    <div class="min-h-screen lg:ml-64">
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

    @if ($errors->any() || session()->has('status'))
        <div
            id="session-alert"
            data-alert-type="{{ $errors->any() ? 'error' : 'success' }}"
            data-alert-message="{{ $errors->any() ? $errors->first() : session('status') }}"
            hidden
        ></div>
    @endif

    <div id="app-alert-toast" role="status" aria-live="polite" aria-atomic="true" hidden class="fixed right-4 top-4 z-[80] hidden w-[min(24rem,calc(100vw-2rem))] items-start gap-3 rounded-xl border border-emerald-200 bg-white p-4 text-slate-800 shadow-xl">
        <span data-alert-icon="success" class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-700" aria-hidden="true">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
            </svg>
        </span>
        <span data-alert-icon="error" hidden class="mt-0.5 hidden flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-700" aria-hidden="true">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </span>
        <p data-alert-message class="min-w-0 flex-1 pt-1 text-sm font-medium leading-5"></p>
        <button type="button" data-alert-dismiss aria-label="Dismiss message" class="rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/>
            </svg>
        </button>
    </div>

    <dialog id="action-confirmation" aria-labelledby="action-confirmation-title" aria-describedby="action-confirmation-message" class="w-[min(90vw,28rem)] rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-2xl backdrop:bg-slate-950/50 backdrop:backdrop-blur-sm">
        <div class="p-6 sm:p-7">
            <div class="flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-[#172b3a]" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3m.1 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </span>
                <div class="pt-0.5">
                    <h2 id="action-confirmation-title" class="text-lg font-bold tracking-tight text-[#172b3a]">Confirm action</h2>
                    <p id="action-confirmation-message" class="mt-1 text-sm leading-5 text-slate-600"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" data-alert-cancel autofocus class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200">Cancel</button>
                <button type="button" data-alert-confirm class="inline-flex h-10 items-center justify-center rounded-lg bg-[#172b3a] px-4 text-sm font-semibold text-white transition hover:bg-[#10212d] focus:outline-none focus:ring-4 focus:ring-amber-200">Continue</button>
            </div>
        </div>
    </dialog>
</body>
</html>
