@extends('layouts.dashboard')

@section('title', 'Logs & Reports')
@section('breadcrumb', 'Logs & Reports')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Logs &amp; Reports</h1>
            <p class="mt-2 text-sm text-slate-500">Recorded gate verification attempts. No sample log records are generated.</p>
        </div>
        <a href="{{ route('logs.export', request()->query()) }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700">Export CSV</a>
    </div>
    <div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            ['Total Scans', $totalScans],
            ['Successful Scans', $successfulScans],
            ['Denied Scans', $deniedScans],
            ['Invalid QR Attempts', $invalidAttempts],
            ['Expired Pass Attempts', $expiredAttempts],
            ['Suspended / Revoked Alerts', $restrictedAttempts],
        ] as [$label, $count])
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($count) }}</p>
            </article>
        @endforeach
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('logs.index') }}" class="grid gap-3 border-b border-slate-100 p-5 sm:grid-cols-3 lg:grid-cols-6">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Employee, plate, guard, gate" aria-label="Search logs" class="h-10 rounded-lg border border-slate-200 px-3 text-sm sm:col-span-3">
            <input name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" aria-label="Start date" class="h-10 rounded-lg border border-slate-200 px-3 text-sm">
            <input name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" aria-label="End date" class="h-10 rounded-lg border border-slate-200 px-3 text-sm">
            <select name="gate_id" aria-label="Filter by gate" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                <option value="">All gates</option>
                @foreach ($gates as $gate)
                    <option value="{{ $gate->id }}" @selected((string) ($filters['gate_id'] ?? '') === (string) $gate->id)>{{ $gate->name }}</option>
                @endforeach
            </select>
            <input name="scan_result" value="{{ $filters['scan_result'] ?? '' }}" placeholder="Scan result" aria-label="Filter by scan result" class="h-10 rounded-lg border border-slate-200 px-3 text-sm">
            <select name="access_status" aria-label="Filter by access status" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                <option value="">All access statuses</option>
                <option value="granted" @selected(($filters['access_status'] ?? '') === 'granted')>Granted</option>
                <option value="denied" @selected(($filters['access_status'] ?? '') === 'denied')>Denied</option>
            </select>
            <button class="h-10 rounded-lg bg-slate-900 px-4 text-sm font-semibold text-white sm:col-span-3 lg:col-span-6">Apply Filters</button>
        </form>

        @if ($scans->isEmpty())
            <p class="px-6 py-14 text-center text-sm text-slate-500">No gate scan logs match these filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Date / Time</th><th class="px-4 py-3">Employee</th><th class="px-4 py-3">Plate Number</th><th class="px-4 py-3">Gate</th><th class="px-4 py-3">Scanned By</th><th class="px-4 py-3">Scan Result</th><th class="px-4 py-3">Access</th><th class="px-5 py-3 text-right">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($scans as $scan)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $scan->created_at->format('M j, Y H:i:s') }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ $scan->employee?->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-4 font-semibold text-slate-800">{{ $scan->plate_number ?? $scan->vehicle?->plate_number ?? '—' }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $scan->gate?->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $scan->scanner?->name ?? '—' }}</td>
                                <td class="px-4 py-4 font-semibold text-slate-700">{{ $scan->scan_result }}</td>
                                <td class="px-4 py-4"><span class="font-semibold {{ $scan->access_status === 'granted' ? 'text-emerald-700' : 'text-rose-700' }}">{{ ucfirst($scan->access_status) }}</span></td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('logs.show', $scan) }}" class="font-semibold text-blue-700 hover:text-blue-800">View Details</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $scans->links() }}</div>
        @endif
    </section>
@endsection
