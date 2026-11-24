@extends('layouts.dashboard')

@section('title', $vehicle->plate_number)
@section('breadcrumb', 'Vehicle Details')

@section('content')
    @if (session('status'))
        <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('vehicles.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">← Vehicle Registry</a>
            <h1 class="mt-3 text-3xl font-bold text-slate-900">{{ $vehicle->plate_number }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $vehicle->make }} {{ $vehicle->model }} · {{ $vehicle->status }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex h-10 items-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700">Edit Vehicle</a>
            <a href="{{ route('employees.show', $vehicle->employee) }}" class="inline-flex h-10 items-center rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white">View Owner</a>
        </div>
    </div>
    <section class="grid gap-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            'Employee Owner' => $vehicle->employee?->name ?? 'Unknown',
            'Employee ID' => $vehicle->employee?->employee_id ?? '—',
            'Make / Model' => $vehicle->make.' '.$vehicle->model,
            'Color' => $vehicle->color,
            'Vehicle Type' => $vehicle->vehicle_type,
            'Chassis / VIN' => $vehicle->vin ?: 'Not provided',
            'Registration Date' => $vehicle->registration_date->format('M j, Y'),
            'Expiration Date' => $vehicle->expiration_date->format('M j, Y'),
            'Pass Status' => $vehicle->status,
        ] as $label => $value)
            <div><p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</p><p class="mt-1 font-semibold text-slate-800">{{ $value }}</p></div>
        @endforeach
    </section>
    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-bold text-slate-900">Pass Status</h2>
        <form method="POST" action="{{ route('vehicles.status', $vehicle) }}" class="mt-4 flex flex-wrap items-end gap-3">
            @csrf
            <label class="text-sm font-medium text-slate-700">Change status
                <select name="status" class="mt-1 block h-10 rounded-lg border border-slate-300 bg-white px-3">
                    @foreach (['Active', 'Suspended', 'Revoked'] as $option)
                        <option value="{{ $option }}" @selected($vehicle->status === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </label>
            <button class="h-10 rounded-lg border border-slate-300 px-4 text-sm font-semibold hover:bg-slate-50">Update Status</button>
        </form>
        <p class="mt-4 text-sm text-amber-800">QR issuance, renewal, and print stickers are not enabled until the CDO-QR:v1 specification is available.</p>
    </section>
@endsection
