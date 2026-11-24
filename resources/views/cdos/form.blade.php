@extends('layouts.dashboard')

@php
    $isEditing = $cdoRecord->exists;
@endphp

@section('title', $isEditing ? 'Edit CDO record' : 'Add CDO record')
@section('breadcrumb', $isEditing ? 'Edit CDO record' : 'Add CDO record')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-slate-900">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6M9 12h12"/>
            </svg>
            Back to dashboard
        </a>

        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-6 sm:px-8">
                <p class="text-sm font-semibold text-blue-700">CDO RECORDS</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $isEditing ? 'Edit record' : 'Add a CDO record' }}</h1>
                <p class="mt-2 text-sm text-slate-500">For testing only. Please use fictional information.</p>
            </div>

            <form method="POST" action="{{ $isEditing ? route('cdos.update', $cdoRecord) : route('cdos.store') }}" class="space-y-5 px-6 py-7 sm:px-8">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="employee_id" class="mb-2 block text-sm font-semibold text-slate-700">Employee ID</label>
                        <input id="employee_id" name="employee_id" type="text" value="{{ old('employee_id', $cdoRecord->employee_id) }}" maxlength="40" required class="h-11 w-full rounded-lg border border-slate-300 px-3.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('employee_id') border-rose-500 @enderror">
                        @error('employee_id')
                            <p class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $cdoRecord->name) }}" maxlength="120" pattern="[\p{L} ]+" title="Use letters and spaces only." oninput="this.value = this.value.replace(/[^\p{L} ]/gu, '')" required autofocus class="h-11 w-full rounded-lg border border-slate-300 px-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('name') border-rose-500 @enderror" placeholder="e.g. Alex Sample">
                        @error('name')
                            <p class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $cdoRecord->email) }}" maxlength="255" pattern="[^@\s]+@[Gg][Mm][Aa][Ii][Ll]\.[Cc][Oo][Mm]" title="Use a Gmail address ending in @gmail.com." required class="h-11 w-full rounded-lg border border-slate-300 px-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('email') border-rose-500 @enderror" placeholder="alex@gmail.com">
                        @error('email')
                            <p class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="phone_number" class="mb-2 block text-sm font-semibold text-slate-700">Phone number</label>
                        <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number', $cdoRecord->phone_number) }}" pattern="09[0-9]{9}" inputmode="numeric" title="Enter an 11-digit Philippine mobile number starting with 09." oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)" class="h-11 w-full rounded-lg border border-slate-300 px-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('phone_number') border-rose-500 @enderror" placeholder="e.g. 09171234567">
                        @error('phone_number')
                            <p class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="department" class="mb-2 block text-sm font-semibold text-slate-700">Department</label>
                        <input id="department" name="department" type="text" value="{{ old('department', $cdoRecord->department) }}" maxlength="100" pattern="[\p{L} ]+" title="Use letters and spaces only." oninput="this.value = this.value.replace(/[^\p{L} ]/gu, '')" required class="h-11 w-full rounded-lg border border-slate-300 px-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('department') border-rose-500 @enderror" placeholder="e.g. Operations">
                        @error('department')
                            <p class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="position" class="mb-2 block text-sm font-semibold text-slate-700">Position</label>
                        <input id="position" name="position" type="text" value="{{ old('position', $cdoRecord->position) }}" maxlength="100" pattern="[\p{L} ]+" title="Use letters and spaces only." oninput="this.value = this.value.replace(/[^\p{L} ]/gu, '')" required class="h-11 w-full rounded-lg border border-slate-300 px-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('position') border-rose-500 @enderror" placeholder="e.g. Coordinator">
                        @error('position')
                            <p class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                        <select id="status" name="status" required class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 @error('status') border-rose-500 @enderror">
                            <option value="">Select a status</option>
                            <option value="Active" @selected(old('status', $cdoRecord->status) === 'Active')>Active</option>
                            <option value="Inactive" @selected(old('status', $cdoRecord->status) === 'Inactive')>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-rose-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('dashboard') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-blue-700 px-5 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">{{ $isEditing ? 'Save changes' : 'Create record' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
