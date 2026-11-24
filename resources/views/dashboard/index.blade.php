@extends('layouts.dashboard')

@section('title', 'Dashboard Overview')
@section('breadcrumb', 'Dashboard')

@section('content')
    @php
        $registrationMaximum = max(1, $registrationChart->max('count'));
        $scanMaximum = max(1, $scanChart->max(fn (array $day) => max($day['successful'], $day['denied'])));
    @endphp

    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-blue-700">CDO VAMS</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Dashboard Overview</h1>
            <p class="mt-2 text-sm text-slate-500">Live vehicle, employee, and gate activity.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('employees.create') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Add Employee</a>
            <a href="{{ route('vehicles.create') }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-blue-700 px-4 text-sm font-semibold text-white transition hover:bg-blue-800">Register Vehicle</a>
        </div>
    </div>

    @if (session('status'))
        <div role="status" class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            ['Total Employees', $totalEmployees, 'employees.index'],
            ['Total Registered Vehicles', $totalVehicles, 'vehicles.index'],
            ['Active Vehicle Passes', $activePasses, 'vehicles.index'],
            ['Expired Vehicle Passes', $expiredPasses, 'vehicles.index', ['status' => 'Expired']],
            ["Today's Successful Scans", $successfulScansToday, 'logs.index', ['access_status' => 'granted']],
            ["Today's Denied Scans", $deniedScansToday, 'logs.index', ['access_status' => 'denied']],
        ] as $card)
            <a href="{{ route($card[2], $card[3] ?? []) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-200 hover:shadow-md">
                <p class="text-sm font-medium text-slate-500">{{ $card[0] }}</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($card[1]) }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-5 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Vehicle Registration</h2>
                    <p class="mt-1 text-sm text-slate-500">New registrations over the past seven days</p>
                </div>
                <a href="{{ route('vehicles.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View vehicles</a>
            </div>
            <div class="mt-6 flex h-44 items-end gap-3 border-b border-slate-100 pb-2">
                @foreach ($registrationChart as $day)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-xs font-semibold text-slate-600">{{ $day['count'] }}</span>
                        <div class="w-full max-w-10 rounded-t-md bg-blue-600" style="height: {{ (int) round($day['count'] / $registrationMaximum * 100) }}%"></div>
                        <span class="text-[11px] text-slate-400">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Gate Scan Activity</h2>
                    <p class="mt-1 text-sm text-slate-500">Recorded successful and denied scans</p>
                </div>
                <a href="{{ route('logs.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View logs</a>
            </div>
            <div class="mt-4 flex gap-4 text-xs font-medium text-slate-500">
                <span class="inline-flex items-center gap-2"><i class="h-2.5 w-2.5 rounded-full bg-emerald-500"></i>Successful</span>
                <span class="inline-flex items-center gap-2"><i class="h-2.5 w-2.5 rounded-full bg-rose-500"></i>Denied</span>
            </div>
            <div class="mt-3 flex h-36 items-end gap-3 border-b border-slate-100 pb-2">
                @foreach ($scanChart as $day)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <div class="flex h-full w-full max-w-10 items-end gap-1">
                            <div class="w-1/2 rounded-t bg-emerald-500" style="height: {{ (int) round($day['successful'] / $scanMaximum * 100) }}%"></div>
                            <div class="w-1/2 rounded-t bg-rose-500" style="height: {{ (int) round($day['denied'] / $scanMaximum * 100) }}%"></div>
                        </div>
                        <span class="text-[11px] text-slate-400">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
            <div>
                <h2 class="font-bold text-slate-900">Recent Gate Verification</h2>
                <p class="mt-1 text-sm text-slate-500">Latest recorded scan attempts</p>
            </div>
            <a href="{{ route('logs.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View Logs</a>
        </div>
        @if ($recentScans->isEmpty())
            <p class="px-6 py-10 text-center text-sm text-slate-500">No gate scans have been recorded.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr><th class="px-6 py-3">Date / Time</th><th class="px-4 py-3">Employee</th><th class="px-4 py-3">Plate</th><th class="px-4 py-3">Gate</th><th class="px-4 py-3">Result</th><th class="px-6 py-3 text-right">Details</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentScans as $scan)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-3 text-slate-600">{{ $scan->created_at->format('M j, Y H:i') }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $scan->employee?->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $scan->plate_number ?? $scan->vehicle?->plate_number ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $scan->gate?->name ?? '—' }}</td>
                                <td class="px-4 py-3 font-semibold {{ $scan->access_status === 'granted' ? 'text-emerald-700' : 'text-rose-700' }}">{{ $scan->scan_result }}</td>
                                <td class="px-6 py-3 text-right"><a href="{{ route('logs.show', $scan) }}" class="font-semibold text-blue-700 hover:text-blue-800">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <div class="mt-6 grid gap-5 xl:grid-cols-2">
        <section class="rounded-2xl border border-amber-200 bg-amber-50/60 p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-900">Expired Pass Alerts</h2>
                <a href="{{ route('vehicles.index', ['status' => 'expired']) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View Expired Vehicles</a>
            </div>
            @forelse ($expiredVehicles as $vehicle)
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-amber-100 pt-3 text-sm">
                    <span><strong class="text-slate-800">{{ $vehicle->plate_number }}</strong><span class="block text-slate-500">{{ $vehicle->employee?->name ?? 'Unknown owner' }}</span></span>
                    <span class="shrink-0 text-amber-800">Expired {{ $vehicle->expiration_date->format('M j, Y') }}</span>
                </div>
            @empty
                <p class="mt-4 text-sm text-slate-600">No expired vehicle passes.</p>
            @endforelse
        </section>
        <section class="rounded-2xl border border-rose-200 bg-rose-50/50 p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-900">Suspended &amp; Revoked Alerts</h2>
                <a href="{{ route('vehicles.index', ['status' => 'Suspended']) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View Registry</a>
            </div>
            @forelse ($restrictedVehicles as $vehicle)
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-rose-100 pt-3 text-sm">
                    <span><strong class="text-slate-800">{{ $vehicle->plate_number }}</strong><span class="block text-slate-500">{{ $vehicle->employee?->name ?? 'Unknown owner' }}</span></span>
                    <span class="shrink-0 font-semibold text-rose-700">{{ $vehicle->status }}</span>
                </div>
            @empty
                @if ($restrictedEmployees->isEmpty())
                    <p class="mt-4 text-sm text-slate-600">No suspended or revoked vehicles or employees.</p>
                @endif
            @endforelse
            @foreach ($restrictedEmployees as $employee)
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-rose-100 pt-3 text-sm">
                    <span><strong class="text-slate-800">{{ $employee->name }}</strong><span class="block text-slate-500">{{ $employee->employee_id }}</span></span>
                    <span class="shrink-0 font-semibold text-rose-700">{{ $employee->status }}</span>
                </div>
            @endforeach
        </section>
    </div>
@endsection
