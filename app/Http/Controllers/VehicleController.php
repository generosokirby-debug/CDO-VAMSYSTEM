<?php

namespace App\Http\Controllers;

use App\Models\CdoRecord;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['Active', 'Suspended', 'Revoked', 'Expired'])],
            'vehicle_type' => ['nullable', 'string', 'max:40'],
        ]);

        return view('vehicles.index', [
            'vehicles' => $this->filteredQuery($filters)
                ->with('employee')
                ->latest('registration_date')
                ->paginate(10)
                ->withQueryString(),
            'search' => $filters['search'] ?? '',
            'status' => $filters['status'] ?? '',
            'vehicleType' => $filters['vehicle_type'] ?? '',
        ]);
    }

    public function create(Request $request): View
    {
        return view('vehicles.form', [
            'vehicle' => new Vehicle,
            'employees' => CdoRecord::where('status', 'Active')->orderBy('name')->get(),
            'selectedEmployee' => $request->query('employee_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'plate_number' => Str::upper(trim((string) $request->input('plate_number'))),
        ]);

        $data = $request->validate($this->rules());
        $data['expiration_date'] = Carbon::parse($data['registration_date'])->addYears(5)->toDateString();
        $vehicle = Vehicle::create($data);

        return redirect()->route('vehicles.show', $vehicle)->with('status', 'Vehicle registered. Its pass expires five years after registration.');
    }

    public function show(Vehicle $vehicle): View
    {
        $vehicle->load('employee');

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.form', [
            'vehicle' => $vehicle,
            'employees' => CdoRecord::where('status', 'Active')->orderBy('name')->get(),
            'selectedEmployee' => $vehicle->employee_id,
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $request->merge([
            'plate_number' => Str::upper(trim((string) $request->input('plate_number'))),
        ]);

        $data = $request->validate($this->rules($vehicle));
        $data['expiration_date'] = Carbon::parse($data['registration_date'])->addYears(5)->toDateString();
        $vehicle->update($data);

        return redirect()->route('vehicles.show', $vehicle)->with('status', 'Vehicle information updated.');
    }

    public function updateStatus(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['Active', 'Suspended', 'Revoked'])],
        ]);
        $vehicle->update($data);

        return back()->with('status', 'Vehicle status updated.');
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['Active', 'Suspended', 'Revoked', 'Expired'])],
            'vehicle_type' => ['nullable', 'string', 'max:40'],
        ]);
        $vehicles = $this->filteredQuery($filters)->with('employee')->lazyById(500);

        return response()->streamDownload(function () use ($vehicles): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['License Plate', 'Employee Owner', 'Make', 'Model', 'Color', 'Vehicle Type', 'Registration Date', 'Expiration Date', 'Status']);

            foreach ($vehicles as $vehicle) {
                fputcsv($output, [
                    $vehicle->plate_number,
                    $vehicle->employee?->name,
                    $vehicle->make,
                    $vehicle->model,
                    $vehicle->color,
                    $vehicle->vehicle_type,
                    $vehicle->registration_date->toDateString(),
                    $vehicle->expiration_date->toDateString(),
                    $vehicle->status,
                ]);
            }

            fclose($output);
        }, 'vehicles.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Vehicle>
     */
    private function filteredQuery(array $filters): Builder
    {
        return Vehicle::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('plate_number', 'like', "%{$search}%")
                        ->orWhere('make', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhereHas('employee', function (Builder $employeeQuery) use ($search): void {
                            $employeeQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('employee_id', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, function (Builder $query, string $status): void {
                if ($status === 'Expired') {
                    $query->whereDate('expiration_date', '<', today());

                    return;
                }

                $query->where('status', $status);
            })
            ->when($filters['vehicle_type'] ?? null, fn (Builder $query, string $type): Builder => $query->where('vehicle_type', $type));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Vehicle $vehicle = null): array
    {
        return [
            'employee_id' => ['required', 'integer', Rule::exists('cdo_records', 'id')->where('status', 'Active')],
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate_number')->ignore($vehicle)],
            'make' => ['required', 'string', 'max:80'],
            'model' => ['required', 'string', 'max:80'],
            'color' => ['required', 'string', 'max:50'],
            'vehicle_type' => ['required', 'string', 'max:40'],
            'vin' => ['nullable', 'string', 'max:80', Rule::unique('vehicles', 'vin')->ignore($vehicle)],
            'registration_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Active', 'Suspended', 'Revoked'])],
        ];
    }
}
