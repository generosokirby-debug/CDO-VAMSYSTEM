<?php

namespace App\Http\Controllers;

use App\Models\Gate;
use App\Models\GateScan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'gate_id' => ['nullable', 'integer', Rule::exists('gates', 'id')],
            'scan_result' => ['nullable', 'string', 'max:40'],
            'access_status' => ['nullable', Rule::in(['granted', 'denied'])],
        ]);
        $query = $this->filteredQuery($filters);

        return view('logs.index', [
            'scans' => (clone $query)->with(['employee', 'vehicle', 'gate', 'scanner'])->latest()->paginate(15)->withQueryString(),
            'gates' => Gate::orderBy('name')->get(),
            'filters' => $filters,
            'totalScans' => (clone $query)->count(),
            'successfulScans' => (clone $query)->where('access_status', 'granted')->count(),
            'deniedScans' => (clone $query)->where('access_status', 'denied')->count(),
            'invalidAttempts' => (clone $query)->where('scan_result', 'FAIL_INVALID')->count(),
            'expiredAttempts' => (clone $query)->where('scan_result', 'FAIL_EXPIRED')->count(),
            'restrictedAttempts' => (clone $query)->whereIn('scan_result', ['ALERT_SUSPENDED', 'ALERT_REVOKED'])->count(),
        ]);
    }

    public function show(GateScan $gateScan): View
    {
        $gateScan->load(['employee', 'vehicle', 'gate', 'scanner']);

        return view('logs.show', ['scan' => $gateScan]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'gate_id' => ['nullable', 'integer', Rule::exists('gates', 'id')],
            'scan_result' => ['nullable', 'string', 'max:40'],
            'access_status' => ['nullable', Rule::in(['granted', 'denied'])],
        ]);
        $scans = $this->filteredQuery($filters)->with(['employee', 'vehicle', 'gate', 'scanner'])->lazyById(500);

        return response()->streamDownload(function () use ($scans): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Date / Time', 'Employee', 'Plate Number', 'Gate', 'Scanned By', 'Scan Result', 'Access Status', 'Remarks']);

            foreach ($scans as $scan) {
                fputcsv($output, [
                    $scan->created_at->toDateTimeString(),
                    $scan->employee?->name,
                    $scan->plate_number ?? $scan->vehicle?->plate_number,
                    $scan->gate?->name,
                    $scan->scanner?->name,
                    $scan->scan_result,
                    $scan->access_status,
                    $scan->remarks,
                ]);
            }

            fclose($output);
        }, 'gate-logs.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<GateScan>
     */
    private function filteredQuery(array $filters): Builder
    {
        return GateScan::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('plate_number', 'like', "%{$search}%")
                        ->orWhereHas('employee', fn (Builder $employeeQuery): Builder => $employeeQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('scanner', fn (Builder $userQuery): Builder => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('gate', fn (Builder $gateQuery): Builder => $gateQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date))
            ->when($filters['gate_id'] ?? null, fn (Builder $query, int $gateId): Builder => $query->where('gate_id', $gateId))
            ->when($filters['scan_result'] ?? null, fn (Builder $query, string $result): Builder => $query->where('scan_result', $result))
            ->when($filters['access_status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('access_status', $status));
    }
}
