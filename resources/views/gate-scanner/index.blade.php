@extends('layouts.dashboard')

@section('title', 'Gate Scanner')
@section('breadcrumb', 'Gate Scanner')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Gate Scanner</h1>
        <p class="mt-2 text-sm text-slate-500">Admin panel verification workspace.</p>
    </div>
    <section class="max-w-3xl rounded-2xl border border-amber-200 bg-amber-50 p-6">
        <h2 class="font-bold text-amber-900">Scanner verification is not enabled</h2>
        <p class="mt-2 text-sm leading-6 text-amber-900">The CDO-QR:v1 payload, canonical encoding, HMAC construction, and renewal/invalidation rules were not available. The scanner is intentionally disabled rather than accepting unverifiable or unsigned passes. No scan attempts have been fabricated.</p>
        <p class="mt-3 text-sm text-amber-900">Provide the CDO-QR:v1 specification to enable signed pass issuance and backend verification.</p>
    </section>
    <section class="mt-6 max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <label for="gate" class="mb-2 block text-sm font-semibold text-slate-700">Active Gate</label>
        <select id="gate" disabled class="h-11 w-full rounded-lg border border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">
            <option>Select a gate</option>
            @foreach ($gates as $gate)
                <option>{{ $gate->name }}</option>
            @endforeach
        </select>
        <div class="mt-5 flex min-h-48 items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 text-center text-sm text-slate-500">Camera and manual scanning require the signed QR specification.</div>
    </section>
@endsection
