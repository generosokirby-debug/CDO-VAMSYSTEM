@extends('layouts.dashboard')

@section('title', 'My Vehicles')
@section('breadcrumb', 'My Vehicles')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">My Registered Vehicles</h1>
        <p class="mt-2 text-sm text-slate-500">Vehicles connected to your linked employee profile only.</p>
    </div>
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @if ($vehicles->isEmpty())
            <div class="px-6 py-16 text-center">
                <h2 class="font-semibold text-slate-900">No vehicles assigned</h2>
                <p class="mt-2 text-sm text-slate-500">Your account is not linked to an employee profile with registered vehicles.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Plate Number</th><th class="px-4 py-3">Make / Model</th><th class="px-4 py-3">Pass Status</th><th class="px-4 py-3">Registration</th><th class="px-4 py-3">Expiration</th><th class="px-5 py-3 text-right">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($vehicles as $vehicle)
                            <tr>
                                <td class="px-5 py-4 font-bold text-slate-900">{{ $vehicle->plate_number }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $vehicle->status }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $vehicle->registration_date->format('M j, Y') }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $vehicle->expiration_date->format('M j, Y') }}</td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('vehicles.show', $vehicle) }}" class="font-semibold text-blue-700 hover:text-blue-800">View Vehicle</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $vehicles->links() }}</div>
            <p class="border-t border-slate-100 px-5 py-4 text-sm text-slate-500">QR passes are unavailable until the CDO-QR:v1 specification is supplied.</p>
        @endif
    </section>
@endsection
