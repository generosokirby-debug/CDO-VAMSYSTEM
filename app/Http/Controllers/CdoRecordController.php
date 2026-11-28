<?php

namespace App\Http\Controllers;

use App\Models\CdoRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CdoRecordController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
        ]);

        $records = CdoRecord::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function (Builder $query, string $status): void {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('cdos.index', [
            'records' => $records,
            'totalCount' => CdoRecord::count(),
            'activeCount' => CdoRecord::where('status', 'Active')->count(),
            'inactiveCount' => CdoRecord::where('status', 'Inactive')->count(),
            'search' => $filters['search'] ?? '',
            'status' => $filters['status'] ?? '',
        ]);
    }

    public function create(): View
    {
        return view('cdos.form', ['cdoRecord' => new CdoRecord]);
    }

    public function store(Request $request): RedirectResponse
    {
        CdoRecord::create($request->validate($this->rules(), $this->messages()));

        return redirect()->route('dashboard')->with('status', 'CDO record created.');
    }

    public function edit(CdoRecord $cdoRecord): View
    {
        return view('cdos.form', compact('cdoRecord'));
    }

    public function update(Request $request, CdoRecord $cdoRecord): RedirectResponse
    {
        $cdoRecord->update($request->validate($this->rules($cdoRecord), $this->messages()));

        return redirect()->route('dashboard')->with('status', 'CDO record updated.');
    }

    public function destroy(CdoRecord $cdoRecord): RedirectResponse
    {
        if ($cdoRecord->vehicles()->exists()) {
            return back()->withErrors(['record' => 'Remove assigned vehicles first.']);
        }

        $cdoRecord->delete();

        return redirect()->route('dashboard')->with('status', 'CDO record deleted.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?CdoRecord $cdoRecord = null): array
    {
        return [
            'employee_id' => ['required', 'string', 'max:40', Rule::unique('cdo_records', 'employee_id')->ignore($cdoRecord?->id)],
            'name' => ['required', 'string', 'max:120', 'regex:/^[\p{L} ]+$/u'],
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[^@\s]+@gmail\.com$/i',
                Rule::unique('cdo_records', 'email')->ignore($cdoRecord?->id),
            ],
            'phone_number' => ['nullable', 'string', 'regex:/^09\d{9}$/'],
            'department' => ['required', 'string', 'max:100', 'regex:/^[\p{L} ]+$/u'],
            'position' => ['required', 'string', 'max:100', 'regex:/^[\p{L} ]+$/u'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'name.regex' => 'Use letters and spaces only.',
            'email.regex' => 'Use a Gmail address ending in @gmail.com.',
            'phone_number.regex' => 'Use an 11-digit mobile number starting with 09.',
            'department.regex' => 'Use letters and spaces only.',
            'position.regex' => 'Use letters and spaces only.',
        ];
    }
}
