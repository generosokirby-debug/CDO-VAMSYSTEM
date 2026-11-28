@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold text-blue-700">CDO STAFF PORTAL</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Dashboard</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Manage sample CDO staff records in this testing workspace.</p>
        </div>
        <a href="{{ route('cdos.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
            </svg>
            Add CDO record
        </a>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total records</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalCount) }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8-3a4 4 0 0 1 0 8m2 1a4 4 0 0 1 2 3.5V21"/>
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-400">All CDO sample entries</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Active</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($activeCount) }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7 10 17l-5-5"/>
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-400">Currently marked active</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Inactive</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($inactiveCount) }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" d="M8 12h8"/>
                    </svg>
                </span>
            </div>
            <p class="mt-3 text-xs text-slate-400">Currently marked inactive</p>
        </article>
    </div>

    <section id="records" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">CDO records</h2>
                <p class="mt-1 text-sm text-slate-500">Create, review, and update sample staff entries.</p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col gap-2 sm:flex-row">
                <label class="relative block">
                    <span class="sr-only">Search CDO records</span>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" d="m20 20-4-4"/>
                    </svg>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search records..." class="h-10 w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 sm:w-56">
                </label>
                <label>
                    <span class="sr-only">Filter by status</span>
                    <select name="status" class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 sm:w-36">
                        <option value="">All statuses</option>
                        <option value="Active" @selected($status === 'Active')>Active</option>
                        <option value="Inactive" @selected($status === 'Inactive')>Inactive</option>
                    </select>
                </label>
                <button type="submit" class="h-10 rounded-lg border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Filter</button>
            </form>
        </div>

        @if ($records->isEmpty())
            <div class="px-6 py-16 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                    </svg>
                </span>
                <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $search || $status ? 'No matching records' : 'No CDO records yet' }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $search || $status ? 'Try another search or clear the selected filters.' : 'Add a sample record to try out the CRUD features.' }}</p>
                @unless ($search || $status)
                    <a href="{{ route('cdos.create') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-blue-700 hover:text-blue-800">Create your first record <span aria-hidden="true">→</span></a>
                @endunless
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th scope="col" class="px-6 py-3.5">Name</th>
                            <th scope="col" class="px-5 py-3.5">Department</th>
                            <th scope="col" class="px-5 py-3.5">Position</th>
                            <th scope="col" class="px-5 py-3.5">Status</th>
                            <th scope="col" class="px-5 py-3.5">Added</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($records as $record)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-700">{{ strtoupper(substr($record->name, 0, 1)) }}</span>
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-slate-900">{{ $record->name }}</span>
                                            <span class="mt-0.5 block truncate text-xs text-slate-500">{{ $record->email }}</span>
                                            @if ($record->phone_number)
                                                <span class="mt-0.5 block truncate text-xs text-slate-500">{{ $record->phone_number }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $record->department }}</td>
                                <td class="px-5 py-4 text-sm text-slate-600">{{ $record->position }}</td>
                                <td class="px-5 py-4">
                                    <span @class([
                                        'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-emerald-50 text-emerald-700' => $record->status === 'Active',
                                        'bg-slate-100 text-slate-600' => $record->status === 'Inactive',
                                    ])>
                                        <span @class([
                                            'h-1.5 w-1.5 rounded-full',
                                            'bg-emerald-500' => $record->status === 'Active',
                                            'bg-slate-400' => $record->status === 'Inactive',
                                        ])></span>
                                        {{ $record->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-500">{{ $record->created_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('cdos.edit', $record) }}" class="rounded-lg px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-50">Edit</a>
                                        <form method="POST" action="{{ route('cdos.destroy', $record) }}" data-alert-action="delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($records->hasPages())
                <div class="border-t border-slate-100 px-5 py-4 sm:px-6">
                    {{ $records->links() }}
                </div>
            @endif
        @endif
    </section>
    <p class="mt-5 text-center text-xs text-slate-400">Testing workspace only · Do not enter real CDO or personal information.</p>
@endsection
