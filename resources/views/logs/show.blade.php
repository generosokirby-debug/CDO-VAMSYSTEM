@extends('layouts.dashboard')

@section('title', 'Gate Scan Details')
@section('breadcrumb', 'Logs & Reports')

@section('content')
    <a href="{{ route('logs.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-900">← Back to logs</a>
    <section class="mt-5 max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Verification Details</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $scan->created_at->format('M j, Y H:i:s') }}</p>
            </div>
            <span class="rounded-full px-3 py-1 text-sm font-bold {{ $scan->access_status === 'granted' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ ucfirst($scan->access_status) }}</span>
        </div>
        <dl class="grid gap-5 pt-5 sm:grid-cols-2">
            @foreach ([
                'Scan Result' => $scan->scan_result,
                'Plate Number' => $scan->plate_number ?? $scan->vehicle?->plate_number ?? '—',
                'Employee' => $scan->employee?->name ?? 'Unknown',
                'Employee ID' => $scan->employee?->employee_id ?? '—',
                'Gate' => $scan->gate?->name ?? '—',
                'Scanned By' => $scan->scanner?->name ?? '—',
                'Vehicle Status' => $scan->vehicle?->status ?? '—',
                'Remarks' => $scan->remarks ?: '—',
            ] as $label => $value)
                <div><dt class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</dt><dd class="mt-1 font-medium text-slate-800">{{ $value }}</dd></div>
            @endforeach
        </dl>
    </section>
@endsection
