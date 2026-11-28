@extends('layouts.dashboard')

@section('title', 'My Profile')
@section('breadcrumb', 'My Profile')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">My Profile</h1>
        <p class="mt-2 text-sm text-slate-500">Update your account information and password.</p>
    </div>
    <div class="grid max-w-4xl gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-slate-900">Account Details</h2>
            <dl class="mt-5 grid gap-4 border-b border-slate-100 pb-5 sm:grid-cols-2">
                <div><dt class="text-xs font-bold uppercase text-slate-400">Role</dt><dd class="mt-1 font-semibold text-slate-800">{{ $user->role }}</dd></div>
                <div><dt class="text-xs font-bold uppercase text-slate-400">Account Status</dt><dd class="mt-1 font-semibold text-slate-800">{{ $user->status }}</dd></div>
            </dl>
            <form method="POST" action="{{ route('profile.update') }}" data-alert-action="update" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label for="profile-name" class="mb-1.5 block text-sm font-semibold text-slate-700">Name</label>
                    <input id="profile-name" name="name" value="{{ old('name', $user->name) }}" required class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <div>
                    <label for="profile-email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                    <input id="profile-email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <button class="h-10 rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white hover:bg-blue-800">Save Profile</button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-bold text-slate-900">Change Password</h2>
            <form method="POST" action="{{ route('profile.password') }}" data-alert-action="update" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="current-password" class="mb-1.5 block text-sm font-semibold text-slate-700">Current Password</label>
                    <input id="current-password" name="current_password" type="password" required autocomplete="current-password" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <div>
                    <label for="new-password" class="mb-1.5 block text-sm font-semibold text-slate-700">New Password</label>
                    <input id="new-password" name="password" type="password" required minlength="12" autocomplete="new-password" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <div>
                    <label for="new-password-confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Confirm New Password</label>
                    <input id="new-password-confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password" class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                </div>
                <button class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-700">Change Password</button>
            </form>
        </section>
    </div>
@endsection
