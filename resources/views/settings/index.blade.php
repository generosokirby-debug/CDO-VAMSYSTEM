@extends('layouts.dashboard')

@section('title', 'Settings & Permissions')
@section('breadcrumb', 'Settings')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Settings &amp; Permissions</h1>
        <p class="mt-2 text-sm text-slate-500">Manage user accounts and page access. Super Admin protections cannot be changed here.</p>
    </div>
    @if (session('status'))
        <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Please check the form and try again.</div>
    @endif

    <nav class="sticky top-16 z-10 mb-6 flex gap-2 overflow-x-auto rounded-xl border border-slate-200 bg-white p-2 shadow-sm" aria-label="Settings sections">
        <a href="#user-accounts" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">User Accounts</a>
        <a href="#roles-permissions" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Roles &amp; Permissions</a>
        <a href="#user-overrides" class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">User Overrides</a>
    </nav>

    <section id="user-accounts" class="scroll-mt-36 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-900">User Accounts</h2>
        </div>
        <form method="POST" action="{{ route('settings.users.store') }}" class="grid gap-3 border-b border-slate-100 bg-slate-50/70 p-5 sm:grid-cols-2 lg:grid-cols-6">
            @csrf
            <input name="name" value="{{ old('name') }}" required placeholder="Full name" aria-label="New user's full name" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm lg:col-span-2">
            <input name="email" type="email" value="{{ old('email') }}" required placeholder="Email address" aria-label="New user's email" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm lg:col-span-2">
            <input name="password" type="password" required minlength="12" placeholder="Temporary password" aria-label="New user's password" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
            <input name="password_confirmation" type="password" required minlength="12" placeholder="Confirm password" aria-label="Confirm new user's password" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
            <select name="role" required aria-label="New user's role" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                @foreach (['Admin', 'Guard', 'Employee'] as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
            <select name="status" required aria-label="New user's status" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
            <select name="employee_record_id" aria-label="Linked employee profile" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                <option value="">No linked employee profile</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->employee_id }} — {{ $employee->name }}</option>
                @endforeach
            </select>
            <button class="h-10 rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white hover:bg-blue-800 lg:col-span-6">Add User</button>
        </form>

        <form method="GET" action="{{ route('settings.index') }}" class="grid gap-3 border-b border-slate-100 p-5 sm:grid-cols-4">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name or email" aria-label="Search users" class="h-10 rounded-lg border border-slate-200 px-3 text-sm">
            <select name="role_filter" aria-label="Filter users by role" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                <option value="">All roles</option>
                @foreach (['Super Admin', 'Admin', 'Guard', 'Employee'] as $role)
                    <option value="{{ $role }}" @selected(($filters['role_filter'] ?? '') === $role)>{{ $role }}</option>
                @endforeach
            </select>
            <select name="status_filter" aria-label="Filter users by status" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                <option value="">All statuses</option>
                @foreach (['Active', 'Inactive'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status_filter'] ?? '') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button class="h-10 rounded-lg border border-slate-200 px-4 text-sm font-semibold hover:bg-slate-50">Search Users</button>
        </form>

        <div class="divide-y divide-slate-100">
            @foreach ($users as $account)
                <div class="flex flex-col justify-between gap-4 px-5 py-4 lg:flex-row lg:items-center">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-900">{{ $account->name }} @if ($account->is(auth()->user()))<span class="text-xs font-medium text-slate-400">(you)</span>@endif</p>
                        <p class="truncate text-sm text-slate-500">{{ $account->email }}</p>
                    </div>
                    @if ($account->role === 'Super Admin')
                        <div class="text-sm font-semibold text-slate-600">Super Admin · {{ $account->status }} <span class="text-xs text-slate-400">Protected</span></div>
                    @else
                        <form method="POST" action="{{ route('settings.users.update', $account) }}" class="flex flex-wrap items-center gap-2">
                            @csrf
                            @method('PATCH')
                            @if ($account->is(auth()->user()))
                                <input type="hidden" name="role" value="{{ $account->role }}">
                                <span class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">{{ $account->role }}</span>
                            @else
                                <label class="sr-only" for="role-{{ $account->id }}">Role</label>
                                <select id="role-{{ $account->id }}" name="role" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                                    @foreach (['Admin', 'Guard', 'Employee'] as $role)
                                        <option value="{{ $role }}" @selected($account->role === $role)>{{ $role }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <label class="sr-only" for="status-{{ $account->id }}">Account status</label>
                            @if ($account->is(auth()->user()))
                                <input type="hidden" name="status" value="{{ $account->status }}">
                                <span class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">{{ $account->status }}</span>
                            @else
                                <label class="sr-only" for="status-{{ $account->id }}">Account status</label>
                                <select id="status-{{ $account->id }}" name="status" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                                    @foreach (['Active', 'Inactive'] as $status)
                                        <option value="{{ $status }}" @selected($account->status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <label class="sr-only" for="employee-{{ $account->id }}">Linked employee profile</label>
                            <select id="employee-{{ $account->id }}" name="employee_record_id" class="h-10 max-w-56 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                                <option value="">No employee profile</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected($account->employee_record_id === $employee->id)>{{ $employee->employee_id }} — {{ $employee->name }}</option>
                                @endforeach
                            </select>
                            <button class="h-10 rounded-lg border border-slate-300 px-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Save</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="border-t border-slate-100 px-5 py-4">{{ $users->links() }}</div>
    </section>

    <section id="roles-permissions" class="mt-6 scroll-mt-36 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-900">Roles &amp; Permissions</h2>
            <p class="mt-1 text-sm text-slate-500">Admins may edit Guard and Employee permissions. Admin and Super Admin access is protected.</p>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach ($roles as $role)
                @php($rolePermissions = $permissions->get($role->role, collect())->keyBy('module'))
                <form method="POST" action="{{ route('settings.permissions.update') }}" class="grid gap-4 p-5 lg:grid-cols-[140px_1fr_auto] lg:items-center">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role->role }}">
                    <h3 class="font-bold text-slate-900">{{ $role->role }}</h3>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($modules as $module => $label)
                            @php($locked = in_array($role->role, ['Admin', 'Super Admin'], true) || $module === 'settings')
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="modules[]" value="{{ $module }}" @checked((bool) ($rolePermissions->get($module)?->allowed ?? false)) @disabled($locked || ! in_array($role->role, ['Guard', 'Employee'], true)) class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                                {{ $label }}
                                @if ($role->role === 'Super Admin' && $module === 'settings')
                                    <span class="text-xs text-slate-400">Locked</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @if (in_array($role->role, ['Guard', 'Employee'], true))
                        <button class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-700">Save Permissions</button>
                    @else
                        <span class="text-xs font-semibold text-slate-400">Protected</span>
                    @endif
                </form>
            @endforeach
        </div>
    </section>

    <section id="user-overrides" class="mt-6 scroll-mt-36 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-bold text-slate-900">User Permission Overrides</h2>
            <p class="mt-1 text-sm text-slate-500">Overrides are limited to modules already granted to the user's role; they cannot elevate access.</p>
        </div>
        @forelse ($users->getCollection()->whereIn('role', ['Guard', 'Employee']) as $account)
            @php($rolePermissions = $permissions->get($account->role, collect())->where('allowed', true)->pluck('module'))
            <form method="POST" action="{{ route('settings.users.overrides', $account) }}" class="flex flex-col gap-4 border-b border-slate-100 p-5 last:border-0 lg:flex-row lg:items-center lg:justify-between">
                @csrf
                <div>
                    <p class="font-semibold text-slate-900">{{ $account->name }}</p>
                    <p class="text-sm text-slate-500">{{ $account->role }}</p>
                </div>
                <div class="flex flex-wrap gap-4">
                    @foreach ($rolePermissions as $module)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="modules[]" value="{{ $module }}" @checked((bool) ($userOverrides->get($account->id, collect())->firstWhere('module', $module)?->allowed ?? true)) class="rounded border-slate-300 text-blue-700">
                            {{ $modules[$module] }}
                        </label>
                    @endforeach
                </div>
                <button class="h-10 rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Save Overrides</button>
            </form>
        @empty
            <p class="px-5 py-8 text-sm text-slate-500">There are no Guard or Employee accounts to configure.</p>
        @endforelse
    </section>
@endsection
