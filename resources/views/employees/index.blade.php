@extends('layouts.dashboard')

@section('title', 'Employees')
@section('breadcrumb', 'Employees')

@section('content')
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Employees</h1>
            <p class="mt-2 text-sm text-slate-500">Manage employee records and their registered vehicles.</p>
        </div>
        <a href="{{ route('employees.create') }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-blue-700 px-5 text-sm font-semibold text-white hover:bg-blue-800">Add Employee</a>
    </div>

    @if (session('status'))
        <div role="status" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Please review the highlighted form errors.</div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('employees.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row">
            <label class="sr-only" for="employee-search">Search employees</label>
            <input id="employee-search" type="search" name="search" value="{{ $search }}" placeholder="Search name, employee ID, department..." class="h-10 min-w-0 flex-1 rounded-lg border border-slate-200 px-3 text-sm">
            <label class="sr-only" for="employee-status">Filter by status</label>
            <select id="employee-status" name="status" class="h-10 rounded-lg border border-slate-200 bg-white px-3 text-sm">
                <option value="">All statuses</option>
                @foreach (['Active', 'Suspended', 'Revoked'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                @endforeach
            </select>
            <button class="h-10 rounded-lg border border-slate-200 px-4 text-sm font-semibold hover:bg-slate-50">Search</button>
        </form>

        @if ($employees->isEmpty())
            <p class="px-6 py-14 text-center text-sm text-slate-500">No employees match your search.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Employee ID</th><th class="px-4 py-3">Full Name</th><th class="px-4 py-3">Department / Position</th><th class="px-4 py-3">Email / Phone</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Vehicles</th><th class="px-5 py-3 text-right">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($employees as $employee)
                            <tr>
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-800">{{ $employee->employee_id }}</td>
                                <td class="px-4 py-4 font-semibold text-slate-800">{{ $employee->name }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $employee->department }}<span class="block text-xs text-slate-400">{{ $employee->position }}</span></td>
                                <td class="px-4 py-4 text-slate-600">{{ $employee->email }}<span class="block text-xs text-slate-400">{{ $employee->phone_number ?: 'No phone listed' }}</span></td>
                                <td class="px-4 py-4"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $employee->status }}</span></td>
                                <td class="px-4 py-4">{{ $employee->vehicles_count }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('employees.show', $employee) }}" class="font-semibold text-blue-700 hover:text-blue-800">View</a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="ml-3 font-semibold text-slate-600 hover:text-slate-900">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $employees->links() }}</div>
        @endif
    </section>
@endsection
