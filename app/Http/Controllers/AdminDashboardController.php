<?php

namespace App\Http\Controllers;

use App\Models\CdoRecord;
use App\Models\GateScan;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();
        $chartStart = $today->copy()->subDays(6)->startOfDay();
        $chartEnd = $today->copy()->endOfDay();

        $dates = collect(range(6, 0))
            ->map(fn (int $daysAgo): Carbon => $today->copy()->subDays($daysAgo));

        $vehicleRegistrations = Vehicle::query()
            ->whereBetween('registration_date', [$chartStart->toDateString(), $today->toDateString()])
            ->get(['registration_date'])
            ->countBy(fn (Vehicle $vehicle): string => $vehicle->registration_date->toDateString());

        $scanActivity = GateScan::query()
            ->whereBetween('created_at', [$chartStart, $chartEnd])
            ->get(['created_at', 'access_status'])
            ->groupBy(fn (GateScan $scan): string => $scan->created_at->toDateString());

        return view('dashboard.index', [
            'totalEmployees' => CdoRecord::count(),
            'totalVehicles' => Vehicle::count(),
            'activePasses' => Vehicle::query()
                ->where('status', 'Active')
                ->whereDate('expiration_date', '>=', $today)
                ->whereHas('employee', fn ($query) => $query->where('status', 'Active'))
                ->count(),
            'expiredPasses' => Vehicle::whereDate('expiration_date', '<', $today)->count(),
            'successfulScansToday' => GateScan::query()
                ->whereDate('created_at', $today)
                ->where('access_status', 'granted')
                ->count(),
            'deniedScansToday' => GateScan::query()
                ->whereDate('created_at', $today)
                ->where('access_status', 'denied')
                ->count(),
            'registrationChart' => $dates->map(fn (Carbon $date): array => [
                'label' => $date->format('D'),
                'count' => (int) ($vehicleRegistrations[$date->toDateString()] ?? 0),
            ]),
            'scanChart' => $dates->map(fn (Carbon $date): array => [
                'label' => $date->format('D'),
                'successful' => $scanActivity->get($date->toDateString(), collect())
                    ->where('access_status', 'granted')
                    ->count(),
                'denied' => $scanActivity->get($date->toDateString(), collect())
                    ->where('access_status', 'denied')
                    ->count(),
            ]),
            'recentScans' => GateScan::with(['employee', 'vehicle', 'gate', 'scanner'])
                ->latest()
                ->limit(8)
                ->get(),
            'expiredVehicles' => Vehicle::with('employee')
                ->whereDate('expiration_date', '<', $today)
                ->latest('expiration_date')
                ->limit(5)
                ->get(),
            'restrictedVehicles' => Vehicle::with('employee')
                ->whereIn('status', ['Suspended', 'Revoked'])
                ->latest()
                ->limit(5)
                ->get(),
            'restrictedEmployees' => CdoRecord::whereIn('status', ['Suspended', 'Revoked'])
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
