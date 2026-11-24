<?php

namespace App\Http\Controllers;

use App\Models\CdoRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['Active', 'Suspended', 'Revoked'])],
        ]);

        $employees = CdoRecord::query()
            ->withCount('vehicles')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_id', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', [
            'employees' => $employees,
            'search' => $filters['search'] ?? '',
            'status' => $filters['status'] ?? '',
        ]);
    }

    public function create(): View
    {
        return view('employees.form', ['employee' => new CdoRecord]);
    }

    public function store(Request $request): RedirectResponse
    {
        $employee = CdoRecord::create($request->validate($this->rules()));

        return redirect()->route('employees.show', $employee)->with('status', 'Employee registered.');
    }

    public function show(CdoRecord $employee): View
    {
        $employee->load('vehicles');

        return view('employees.show', compact('employee'));
    }

    public function edit(CdoRecord $employee): View
    {
        return view('employees.form', compact('employee'));
    }

    public function update(Request $request, CdoRecord $employee): RedirectResponse
    {
        $employee->update($request->validate($this->rules($employee)));

        return redirect()->route('employees.show', $employee)->with('status', 'Employee information updated.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?CdoRecord $employee = null): array
    {
        return [
            'employee_id' => ['required', 'string', 'max:40', Rule::unique('cdo_records', 'employee_id')->ignore($employee)],
            'name' => ['required', 'string', 'max:120'],
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('cdo_records', 'email')->ignore($employee)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(['Active', 'Suspended', 'Revoked'])],
        ];
    }
}
