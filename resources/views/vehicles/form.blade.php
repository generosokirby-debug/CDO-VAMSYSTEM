@extends('layouts.dashboard')

@php($isEditing = $vehicle->exists)

@section('title', $isEditing ? 'Edit Vehicle' : 'Register Vehicle')
@section('breadcrumb', 'Vehicles')

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('vehicles.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">← Back to vehicles</a>
        <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <h1 class="text-2xl font-bold text-slate-900">{{ $isEditing ? 'Edit Vehicle' : 'Register Vehicle' }}</h1>
                <p class="mt-1 text-sm text-slate-500">Pass expiration is calculated as the registration date plus five years.</p>
            </div>
            <form method="POST" action="{{ $isEditing ? route('vehicles.update', $vehicle) : route('vehicles.store') }}" class="grid gap-5 p-6 sm:grid-cols-2">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif
                <div class="sm:col-span-2">
                    <label for="employee_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Employee Owner</label>
                    <select id="employee_id" name="employee_id" required class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm">
                        <option value="">Select an active employee</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected((string) old('employee_id', $selectedEmployee) === (string) $employee->id)>{{ $employee->employee_id }} — {{ $employee->name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id')<p class="mt-1 text-sm text-rose-600" role="alert">{{ $message }}</p>@enderror
                    @if ($employees->isEmpty())
                        <a href="{{ route('employees.create') }}" class="mt-2 inline-block text-sm font-semibold text-blue-700">Add an active employee first</a>
                    @endif
                </div>
                @foreach ([
                    'plate_number' => ['Plate Number', 'text'],
                    'make' => ['Vehicle Make', 'text'],
                    'model' => ['Vehicle Model', 'text'],
                    'color' => ['Color', 'text'],
                    'vehicle_type' => ['Vehicle Type', 'text'],
                    'vin' => ['Chassis / VIN', 'text'],
                ] as $field => [$label, $type])
                    <div>
                        <label for="{{ $field }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $label }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field, $vehicle->{$field}) }}" {{ $field !== 'vin' ? 'required' : '' }} class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm uppercase focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100 @error($field) border-rose-500 @enderror">
                        @error($field)<p class="mt-1 text-sm text-rose-600" role="alert">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <div>
                    <label for="registration_date" class="mb-1.5 block text-sm font-semibold text-slate-700">Registration Date</label>
                    <input id="registration_date" name="registration_date" type="date" value="{{ old('registration_date', $vehicle->registration_date?->toDateString()) }}" required class="h-11 w-full rounded-lg border border-slate-300 px-3 text-sm">
                    @error('registration_date')<p class="mt-1 text-sm text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="expiration_display" class="mb-1.5 block text-sm font-semibold text-slate-700">Expiration Date (calculated)</label>
                    <input id="expiration_display" type="date" value="{{ old('registration_date', $vehicle->registration_date?->toDateString()) ? \Illuminate\Support\Carbon::parse(old('registration_date', $vehicle->registration_date?->toDateString()))->addYears(5)->toDateString() : '' }}" readonly class="h-11 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-500">
                </div>
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-semibold text-slate-700">Status</label>
                    <select id="status" name="status" required class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm">
                        @foreach (['Active', 'Suspended', 'Revoked'] as $option)
                            <option value="{{ $option }}" @selected(old('status', $vehicle->status ?: 'Active') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5 sm:col-span-2">
                    <a href="{{ route('vehicles.index') }}" class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-semibold text-slate-700">Cancel</a>
                    <button class="h-11 rounded-lg bg-blue-700 px-5 text-sm font-semibold text-white hover:bg-blue-800">{{ $isEditing ? 'Save Changes' : 'Register Vehicle' }}</button>
                </div>
            </form>
        </section>
    </div>
    <script>
        document.getElementById('registration_date').addEventListener('change', (event) => {
            if (!event.target.value) {
                document.getElementById('expiration_display').value = '';
                return;
            }

            const [year, month, day] = event.target.value.split('-').map(Number);
            const date = new Date(year, month - 1, day);
            if (!Number.isNaN(date.getTime())) {
                date.setFullYear(date.getFullYear() + 5);
                document.getElementById('expiration_display').value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
            }
        });
    </script>
@endsection
