@extends('layouts.dashboard')

@section('title', 'Dashboard Overview')
@section('breadcrumb', 'Dashboard')

@section('content')
    @php
        $registrationDemoCounts = [4, 7, 5, 9, 6, 11, 8];
        $hasRegistrationData = $registrationChart->contains(fn (array $day): bool => $day['count'] > 0);
        $registrationChartData = $registrationChart->map(fn (array $day, int $index): array => [
            'label' => $day['label'],
            'count' => $hasRegistrationData ? $day['count'] : $registrationDemoCounts[$index],
        ]);
        $registrationMaximum = max(1, $registrationChartData->max('count'));

        $scanDemoValues = [
            ['successful' => 12, 'denied' => 4],
            ['successful' => 16, 'denied' => 3],
            ['successful' => 13, 'denied' => 6],
            ['successful' => 22, 'denied' => 5],
            ['successful' => 18, 'denied' => 7],
            ['successful' => 24, 'denied' => 4],
            ['successful' => 20, 'denied' => 8],
        ];
        $hasScanData = $scanChart->contains(fn (array $day): bool => $day['successful'] + $day['denied'] > 0);
        $scanChartData = $scanChart->map(fn (array $day, int $index): array => [
            'label' => $day['label'],
            'successful' => $hasScanData ? $day['successful'] : $scanDemoValues[$index]['successful'],
            'denied' => $hasScanData ? $day['denied'] : $scanDemoValues[$index]['denied'],
        ]);
        $scanMaximum = max(1, $scanChartData->max(fn (array $day) => max($day['successful'], $day['denied'])));

        $passChartTotal = $activePasses + $expiredPasses;
        $hasPassData = $passChartTotal > 0;
        $passActiveValue = $hasPassData ? $activePasses : 68;
        $passExpiredValue = $hasPassData ? $expiredPasses : 32;
        $passChartTotal = $passActiveValue + $passExpiredValue;
        $passActivePercent = round($passActiveValue / $passChartTotal * 100, 1);

        $scanTodayTotal = $successfulScansToday + $deniedScansToday;
        $hasTodayScanData = $scanTodayTotal > 0;
        $donutSuccessfulValue = $hasTodayScanData ? $successfulScansToday : 74;
        $donutDeniedValue = $hasTodayScanData ? $deniedScansToday : 26;
        $scanTodayTotal = $donutSuccessfulValue + $donutDeniedValue;
        $scanSuccessPercent = round($donutSuccessfulValue / $scanTodayTotal * 100, 1);
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
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm animate-chart-enter motion-reduce:animate-none sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Vehicle Registration</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $hasRegistrationData ? 'New registrations over the past seven days' : 'Sample data shown; no registrations in this period' }}</p>
                </div>
                <a href="{{ route('vehicles.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View vehicles</a>
            </div>
            <div class="mt-6 flex h-44 items-end gap-3 border-b border-slate-100 pb-2">
                @foreach ($registrationChartData as $day)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-xs font-semibold text-slate-600">{{ $day['count'] }}</span>
                        <div class="w-full max-w-10 origin-bottom rounded-t-md bg-blue-600 animate-chart-grow motion-reduce:animate-none" style="height: {{ (int) round($day['count'] / $registrationMaximum * 100) }}%; animation-delay: {{ $loop->index * 75 }}ms"></div>
                        <span class="text-[11px] text-slate-400">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm animate-chart-enter motion-reduce:animate-none sm:p-6" style="animation-delay: 100ms">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-900">Gate Scan Trend</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $hasScanData ? 'Successful and denied scans over the past seven days' : 'Sample data shown; no scans in this period' }}</p>
                </div>
                <a href="{{ route('logs.index') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View logs</a>
            </div>
            <div class="mt-4 flex gap-4 text-xs font-medium text-slate-500">
                <span class="inline-flex items-center gap-2"><i class="h-2.5 w-2.5 rounded-full bg-blue-500"></i>Successful</span>
                <span class="inline-flex items-center gap-2"><i class="h-2.5 w-2.5 rounded-full bg-red-500"></i>Denied</span>
            </div>
            <div class="mt-3">
                <svg class="h-36 w-full overflow-visible" viewBox="0 0 280 140" role="img" aria-label="Line chart showing successful and denied gate scans over the past seven days">
                    <path d="M20 20H260M20 70H260M20 120H260" stroke="#e2e8f0" stroke-dasharray="4 5" />
                    <polyline
                        points="@foreach ($scanChartData as $index => $day){{ 20 + $index * 40 }},{{ 120 - (int) round($day['successful'] / $scanMaximum * 100) }} @endforeach"
                        fill="none"
                        stroke="#3b82f6"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        pathLength="1000"
                        stroke-dasharray="1000"
                        class="animate-chart-line motion-reduce:animate-none"
                    />
                    <polyline
                        points="@foreach ($scanChartData as $index => $day){{ 20 + $index * 40 }},{{ 120 - (int) round($day['denied'] / $scanMaximum * 100) }} @endforeach"
                        fill="none"
                        stroke="#ef4444"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        pathLength="1000"
                        stroke-dasharray="1000"
                        class="animate-chart-line motion-reduce:animate-none"
                        style="animation-delay: 150ms"
                    />
                    @foreach ($scanChartData as $index => $day)
                        <circle cx="{{ 20 + $index * 40 }}" cy="{{ 120 - (int) round($day['successful'] / $scanMaximum * 100) }}" r="3" fill="#3b82f6" />
                        <circle cx="{{ 20 + $index * 40 }}" cy="{{ 120 - (int) round($day['denied'] / $scanMaximum * 100) }}" r="3" fill="#ef4444" />
                    @endforeach
                </svg>
                <div class="flex justify-between px-1 text-[11px] text-slate-400">
                    @foreach ($scanChartData as $day)
                        <span>{{ $day['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    <div class="mt-5 grid gap-5 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm animate-chart-enter motion-reduce:animate-none sm:p-6" style="animation-delay: 200ms">
            <div>
                <h2 class="font-bold text-slate-900">Vehicle Pass Status</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $hasPassData ? 'Active and expired passes' : 'Sample data shown; no active or expired passes' }}</p>
            </div>
            <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row">
                <div
                    class="size-40 shrink-0 rounded-full animate-chart-pop motion-reduce:animate-none"
                    role="img"
                    aria-label="Pie chart: {{ number_format($passActiveValue) }} active passes and {{ number_format($passExpiredValue) }} expired passes{{ $hasPassData ? '' : ', sample data' }}"
                    style="background: conic-gradient(#10b981 0% {{ $passActivePercent }}%, #f43f5e {{ $passActivePercent }}% 100%)"
                ></div>
                <div class="grid w-full gap-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="inline-flex items-center gap-2 text-sm text-slate-600"><i class="size-3 rounded-sm bg-emerald-500"></i>Active</span>
                        <span class="font-semibold text-slate-900">{{ number_format($passActiveValue) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="inline-flex items-center gap-2 text-sm text-slate-600"><i class="size-3 rounded-sm bg-rose-500"></i>Expired</span>
                        <span class="font-semibold text-slate-900">{{ number_format($passExpiredValue) }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm animate-chart-enter motion-reduce:animate-none sm:p-6" style="animation-delay: 300ms">
            <div>
                <h2 class="font-bold text-slate-900">Today's Scan Outcomes</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $hasTodayScanData ? 'Successful and denied gate scans' : 'Sample data shown; no scans recorded today' }}</p>
            </div>
            <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row">
                <div class="relative size-40 shrink-0 animate-chart-pop motion-reduce:animate-none">
                    <div
                        class="size-full rounded-full"
                        role="img"
                        aria-label="Donut chart: {{ number_format($donutSuccessfulValue) }} successful scans and {{ number_format($donutDeniedValue) }} denied scans{{ $hasTodayScanData ? '' : ', sample data' }}"
                        style="background: conic-gradient(#3b82f6 0% {{ $scanSuccessPercent }}%, #ef4444 {{ $scanSuccessPercent }}% 100%)"
                    ></div>
                    <div class="absolute inset-5 flex flex-col items-center justify-center rounded-full bg-white">
                        <span class="text-2xl font-bold text-slate-900">{{ number_format($scanTodayTotal) }}</span>
                        <span class="text-xs text-slate-500">total scans</span>
                    </div>
                </div>
                <div class="grid w-full gap-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="inline-flex items-center gap-2 text-sm text-slate-600"><i class="size-3 rounded-sm bg-emerald-500"></i>Successful</span>
                        <span class="font-semibold text-slate-900">{{ number_format($donutSuccessfulValue) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="inline-flex items-center gap-2 text-sm text-slate-600"><i class="size-3 rounded-sm bg-rose-500"></i>Denied</span>
                        <span class="font-semibold text-slate-900">{{ number_format($donutDeniedValue) }}</span>
                    </div>
                </div>
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
