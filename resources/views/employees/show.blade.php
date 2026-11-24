@extends('layouts.dashboard')

@section('title', $employee->name)
@section('breadcrumb', 'Employee Details')

@section('content')
    @if (session('status'))
        <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('employees.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">← Employees</a>
            <h1 class="mt-3 text-3xl font-bold text-slate-900">{{ $employee->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $employee->employee_id }} · {{ $employee->status }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('vehicles.create', ['employee_id' => $employee->id]) }}" class="inline-flex h-10 items-center rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white">Register Vehicle</a>
            <a href="{{ route('employees.edit', $employee) }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700">Edit Employee</a>
        </div>
    </div>

    <section class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-2">
        @foreach (['Department' => $employee->department, 'Position' => $employee->position, 'Email' => $employee->email, 'Phone Number' => $employee->phone_number ?: 'Not provided', 'Status' => $employee->status, 'Employee ID' => $employee->employee_id] as $label => $value)
            <div><p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</p><p class="mt-1 font-semibold text-slate-800">{{ $value }}</p></div>
        @endforeach
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="font-bold text-slate-900">Registered Vehicles</h2>
        </div>
        @if ($employee->vehicles->isEmpty())
            <p class="px-6 py-10 text-center text-sm text-slate-500">No vehicles are registered to this employee.</p>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($employee->vehicles as $vehicle)
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-slate-50">
                        <span><span class="font-bold text-slate-900">{{ $vehicle->plate_number }}</span><span class="mt-1 block text-sm text-slate-500">{{ $vehicle->make }} {{ $vehicle->model }} · {{ $vehicle->color }}</span></span>
                        <span class="text-right text-sm"><span class="block font-semibold text-slate-700">{{ $vehicle->status }}</span><span class="text-slate-400">Expires {{ $vehicle->expiration_date->format('M j, Y') }}</span></span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
@endsection
