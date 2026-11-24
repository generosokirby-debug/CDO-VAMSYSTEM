@extends('layouts.dashboard')

@section('title', 'Vehicles & QR Passes')
@section('breadcrumb', 'Vehicles')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Vehicles &amp; Passes</h1>
            <p class="mt-2 text-sm text-slate-500">Manage vehicle registrations and pass statuses.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('vehicles.export', request()->query()) }}" class="inline-flex h-11 items-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700">Export CSV</a>
            <a href="{{ route('vehicles.create') }}" class="inline-flex h-11 items-center rounded-lg bg-blue-700 px-5 text-sm font-semibold text-white hover:bg-blue-800">Register Vehicle</a>
        </div>
    </div>
    @if (session('status'))
        <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Please review the highlighted form errors.</div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('vehicles.index') }}" class="grid gap-3 border-b border-slate-100 p-5 sm:grid-cols-4">
            <label class="sr-only" for="vehicle-search">Search vehicles</label>
            <input id="vehicle-search" type="search" name="search" value="{{ $search }}" placeholder="Plate, owner, make or model" class="h-10 rounded-lg border border-slate-200 px-3 text-sm sm:col-span-2">
            <label class="sr-only" for="vehicle-status">Filter by status</label>
            <select id="vehicle-status" name="status" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                <option value="">All statuses</option>
                @foreach (['Active', 'Suspended', 'Revoked', 'Expired'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <label class="sr-only" for="vehicle-type">Filter by vehicle type</label>
                <input id="vehicle-type" name="vehicle_type" value="{{ $vehicleType }}" placeholder="Vehicle type" class="h-10 min-w-0 flex-1 rounded-lg border border-slate-200 px-3 text-sm">
                <button class="h-10 rounded-lg border border-slate-200 px-4 text-sm font-semibold hover:bg-slate-50">Filter</button>
            </div>
        </form>
        @if ($vehicles->isEmpty())
            <p class="px-6 py-14 text-center text-sm text-slate-500">No vehicles match your filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">License Plate</th><th class="px-4 py-3">Employee Owner</th><th class="px-4 py-3">Make / Model</th><th class="px-4 py-3">Color</th><th class="px-4 py-3">Vehicle Type</th><th class="px-4 py-3">Registration</th><th class="px-4 py-3">Expiration</th><th class="px-4 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($vehicles as $vehicle)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4 font-bold text-slate-900">{{ $vehicle->plate_number }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $vehicle->employee?->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $vehicle->color }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $vehicle->vehicle_type }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-slate-600">{{ $vehicle->registration_date->format('M j, Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-slate-600">{{ $vehicle->expiration_date->format('M j, Y') }}</td>
                                <td class="px-4 py-4"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $vehicle->expiration_date->isBefore(today()) ? 'Expired' : $vehicle->status }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="font-semibold text-blue-700 hover:text-blue-800">View</a>
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="ml-3 font-semibold text-slate-600 hover:text-slate-900">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $vehicles->links() }}</div>
        @endif
    </section>
    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">QR issuance, pass renewal, and gate verification are unavailable until the CDO-QR:v1 signing specification is provided.</p>
@endsection
