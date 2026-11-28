@extends('layouts.dashboard')

@php($isEditing = $employee->exists)

@section('title', $isEditing ? 'Edit Employee' : 'Add Employee')
@section('breadcrumb', 'Employees')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('employees.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">← Back to employees</a>
        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <h1 class="text-2xl font-bold text-slate-900">{{ $isEditing ? 'Edit Employee' : 'Register Employee' }}</h1>
                <p class="mt-1 text-sm text-slate-500">Enter employee information and account status.</p>
            </div>
            <form method="POST" action="{{ $isEditing ? route('employees.update', $employee) : route('employees.store') }}" data-alert-action="{{ $isEditing ? 'update' : 'create' }}" class="grid gap-5 p-6 sm:grid-cols-2">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif
                @foreach ([
                    'employee_id' => ['Employee ID', 'text'],
                    'name' => ['Full Name', 'text'],
                    'department' => ['Department', 'text'],
                    'position' => ['Position', 'text'],
                    'email' => ['Email', 'email'],
                    'phone_number' => ['Phone Number', 'tel'],
                ] as $field => [$label, $type])
                    <div class="{{ in_array($field, ['name', 'email']) ? 'sm:col-span-2' : '' }}">
                        <label for="{{ $field }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field, $employee->{$field}) }}" {{ $field !== 'phone_number' ? 'required' : '' }} class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100 @error($field) border-rose-500 @enderror">
                        @error($field)<p class="mt-1 text-sm text-rose-600" role="alert">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <div class="sm:col-span-2">
                    <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status</label>
                    <select id="status" name="status" required class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm">
                        @foreach (['Active', 'Suspended', 'Revoked'] as $option)
                            <option value="{{ $option }}" @selected(old('status', $employee->status ?: 'Active') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-sm text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5 sm:col-span-2">
                    <a href="{{ route('employees.index') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700">Cancel</a>
                    <button class="h-11 rounded-lg bg-blue-700 px-5 text-sm font-semibold text-white hover:bg-blue-800">{{ $isEditing ? 'Save Changes' : 'Register Employee' }}</button>
                </div>
            </form>
        </section>
    </div>
@endsection
