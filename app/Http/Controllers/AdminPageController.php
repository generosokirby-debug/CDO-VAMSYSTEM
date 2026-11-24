<?php

namespace App\Http\Controllers;

use App\Models\CdoRecord;
use App\Models\Gate;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function myVehicles(Request $request): View
    {
        $employeeId = $request->user()->employee_record_id;
        $vehicles = Vehicle::query()
            ->where('employee_id', $employeeId ?? 0)
            ->with('employee')
            ->latest('registration_date')
            ->paginate(10);

        return view('my-vehicles.index', compact('vehicles'));
    }

    public function settings(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role_filter' => ['nullable', Rule::in(['Super Admin', 'Admin', 'Guard', 'Employee'])],
            'status_filter' => ['nullable', Rule::in(['Active', 'Inactive'])],
        ]);
        $users = User::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role_filter'] ?? null, fn (Builder $query, string $role): Builder => $query->where('role', $role))
            ->when($filters['status_filter'] ?? null, fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('settings.index', [
            'users' => $users,
            'employees' => CdoRecord::where('status', 'Active')->orderBy('name')->get(['id', 'employee_id', 'name']),
            'filters' => $filters,
            'roles' => DB::table('role_permissions')->select('role')->distinct()->orderBy('role')->get(),
            'permissions' => DB::table('role_permissions')->orderBy('role')->orderBy('module')->get()->groupBy('role'),
            'userOverrides' => DB::table('user_permission_overrides')->get()->groupBy('user_id'),
            'modules' => [
                'dashboard' => 'Dashboard',
                'employees' => 'Employees',
                'vehicles' => 'Vehicles',
                'gate-scanner' => 'Gate Scanner',
                'logs' => 'Logs',
                'my-vehicles' => 'My Vehicles',
                'settings' => 'Settings',
            ],
        ]);
    }

    public function gateScanner(): View
    {
        return view('gate-scanner.index', ['gates' => Gate::where('is_active', true)->orderBy('name')->get()]);
    }

    public function profile(Request $request): View
    {
        return view('profile.index', ['user' => $request->user()]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'role' => ['required', Rule::in(['Admin', 'Guard', 'Employee'])],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'employee_record_id' => ['nullable', 'integer', Rule::exists('cdo_records', 'id')->where('status', 'Active')],
        ]);

        User::create($data);

        return back()->with('status', 'User account created.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === 'Super Admin', 403, 'Super Admin accounts cannot be changed here.');
        abort_if(
            $user->is($request->user())
                && ($request->input('role') !== $user->role || $request->input('status') !== $user->status),
            403,
            'You cannot change your own role or account status.',
        );

        $data = $request->validate([
            'role' => ['required', Rule::in(['Admin', 'Guard', 'Employee'])],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'employee_record_id' => ['nullable', 'integer', Rule::exists('cdo_records', 'id')->where('status', 'Active')],
        ]);

        $user->update($data);

        return back()->with('status', 'User account updated.');
    }

    public function updateRolePermissions(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['Guard', 'Employee'])],
            'modules' => ['sometimes', 'array'],
            'modules.*' => ['string', Rule::in(['dashboard', 'employees', 'vehicles', 'gate-scanner', 'logs', 'my-vehicles'])],
        ]);
        $allowedModules = $data['modules'] ?? [];

        foreach (['dashboard', 'employees', 'vehicles', 'gate-scanner', 'logs', 'my-vehicles', 'settings'] as $module) {
            DB::table('role_permissions')->updateOrInsert(
                ['role' => $data['role'], 'module' => $module],
                ['allowed' => in_array($module, $allowedModules, true), 'updated_at' => now(), 'created_at' => now()],
            );
        }

        DB::table('role_permissions')->updateOrInsert(
            ['role' => 'Super Admin', 'module' => 'settings'],
            ['allowed' => true, 'updated_at' => now(), 'created_at' => now()],
        );

        return back()->with('status', 'Permissions saved.');
    }

    public function updateUserOverrides(Request $request, User $user): RedirectResponse
    {
        abort_if(in_array($user->role, ['Admin', 'Super Admin'], true), 403, 'Overrides are only available for Guard and Employee accounts.');

        $data = $request->validate([
            'modules' => ['sometimes', 'array'],
            'modules.*' => ['string', Rule::in(['dashboard', 'employees', 'vehicles', 'gate-scanner', 'logs', 'my-vehicles', 'settings'])],
        ]);
        $modules = $data['modules'] ?? [];
        $roleModules = DB::table('role_permissions')
            ->where('role', $user->role)
            ->where('allowed', true)
            ->pluck('module')
            ->all();

        abort_if(array_diff($modules, $roleModules) !== [], 403, 'An override cannot grant access beyond the assigned role.');

        foreach ($roleModules as $module) {
            DB::table('user_permission_overrides')->updateOrInsert(
                ['user_id' => $user->id, 'module' => $module],
                ['allowed' => in_array($module, $modules, true), 'updated_at' => now(), 'created_at' => now()],
            );
        }

        return back()->with('status', 'User permission overrides saved.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($request->user())],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Password changed.');
    }
}
