<?php

namespace Tests\Feature;

use App\Models\CdoRecord;
use App\Models\GateScan;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_and_non_admins_are_forbidden_from_the_admin_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['role' => 'Guard']))
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_admin_management_pages_render_with_empty_database(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));

        foreach ([
            'employees.index',
            'vehicles.index',
            'logs.index',
            'settings.index',
            'profile.show',
            'gate-scanner.index',
        ] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_guard_can_open_the_scanner_but_not_admin_dashboard_or_employee_vehicles(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Guard']));

        $this->get(route('gate-scanner.index'))->assertOk();
        $this->get(route('dashboard'))->assertForbidden();
        $this->get(route('my-vehicles.index'))->assertForbidden();
    }

    public function test_employee_registration_saves_the_requested_fields(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));

        $this->post(route('employees.store'), [
            'employee_id' => 'EMP-300',
            'name' => 'Jordan Sample',
            'department' => 'Operations',
            'position' => 'Coordinator',
            'email' => 'jordan@example.test',
            'phone_number' => '09171234567',
            'status' => 'Active',
        ])->assertRedirect();

        $this->assertDatabaseHas('cdo_records', [
            'employee_id' => 'EMP-300',
            'name' => 'Jordan Sample',
            'status' => 'Active',
        ]);
    }

    public function test_vehicle_registration_normalizes_plate_and_calculates_expiration(): void
    {
        $employee = CdoRecord::factory()->create([
            'employee_id' => 'EMP-100',
            'status' => 'Active',
        ]);
        $this->actingAs(User::factory()->create(['role' => 'Admin']));

        $this->post(route('vehicles.store'), [
            'employee_id' => $employee->id,
            'plate_number' => '  cdo 123  ',
            'make' => 'Toyota',
            'model' => 'Vios',
            'color' => 'White',
            'vehicle_type' => 'Sedan',
            'vin' => 'VIN-100',
            'registration_date' => '2025-01-15',
            'status' => 'Active',
        ])->assertRedirect(route('vehicles.show', Vehicle::where('plate_number', 'CDO 123')->firstOrFail()));

        $vehicle = Vehicle::where('plate_number', 'CDO 123')->firstOrFail();
        $this->assertSame('2030-01-15', $vehicle->expiration_date->toDateString());
        $this->assertSame($employee->id, $vehicle->employee_id);
    }

    public function test_duplicate_plates_are_rejected_after_normalization(): void
    {
        $employee = CdoRecord::factory()->create(['status' => 'Active']);
        $this->createVehicle($employee, ['plate_number' => 'XYZ 999']);
        $this->actingAs(User::factory()->create(['role' => 'Admin']));

        $this->post(route('vehicles.store'), [
            'employee_id' => $employee->id,
            'plate_number' => ' xyz 999 ',
            'make' => 'Toyota',
            'model' => 'Vios',
            'color' => 'White',
            'vehicle_type' => 'Sedan',
            'registration_date' => '2025-01-15',
            'status' => 'Active',
        ])->assertSessionHasErrors('plate_number');

        $this->assertDatabaseCount('vehicles', 1);
    }

    public function test_admin_cannot_modify_super_admin_or_grant_an_override_outside_a_role(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $superAdmin = User::factory()->create(['role' => 'Super Admin']);
        $guard = User::factory()->create(['role' => 'Guard']);
        $this->actingAs($admin);

        $this->patch(route('settings.users.update', $superAdmin), [
            'role' => 'Admin',
            'status' => 'Inactive',
            'employee_record_id' => '',
        ])->assertForbidden();

        $this->post(route('settings.users.overrides', $guard), [
            'modules' => ['dashboard'],
        ])->assertForbidden();

        $this->assertDatabaseCount('user_permission_overrides', 0);
    }

    public function test_user_accounts_are_hashed_and_role_permissions_are_saved_without_unlocking_super_admin_settings(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));

        $this->post(route('settings.users.store'), [
            'name' => 'New Guard',
            'email' => 'guard@example.test',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'role' => 'Guard',
            'status' => 'Active',
            'employee_record_id' => '',
        ])->assertRedirect();

        $guard = User::where('email', 'guard@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('SecurePassword123!', $guard->password));

        $this->post(route('settings.permissions.update'), [
            'role' => 'Guard',
            'modules' => ['gate-scanner', 'my-vehicles'],
        ])->assertRedirect();

        $this->assertDatabaseHas('role_permissions', [
            'role' => 'Guard',
            'module' => 'my-vehicles',
            'allowed' => true,
        ]);
        $this->assertSame(
            1,
            DB::table('role_permissions')->where('role', 'Super Admin')->where('module', 'settings')->value('allowed'),
        );
    }

    public function test_dashboard_counts_only_database_records_and_my_vehicles_are_owner_scoped(): void
    {
        $this->travelTo('2026-11-23 10:00:00');
        $employee = CdoRecord::factory()->create(['employee_id' => 'EMP-200', 'status' => 'Active']);
        $otherEmployee = CdoRecord::factory()->create(['employee_id' => 'EMP-201', 'status' => 'Active']);
        $activeVehicle = $this->createVehicle($employee, [
            'plate_number' => 'OWN-200',
            'expiration_date' => '2027-01-01',
        ]);
        $this->createVehicle($otherEmployee, [
            'plate_number' => 'OTHER-201',
            'expiration_date' => '2026-01-01',
        ]);
        GateScan::create([
            'vehicle_id' => $activeVehicle->id,
            'employee_id' => $employee->id,
            'scan_result' => 'PASS',
            'access_status' => 'granted',
        ]);
        GateScan::create([
            'vehicle_id' => null,
            'employee_id' => null,
            'plate_number' => 'UNKNOWN-1',
            'scan_result' => 'FAIL_INVALID',
            'access_status' => 'denied',
        ]);
        $admin = User::factory()->create([
            'role' => 'Admin',
            'employee_record_id' => $employee->id,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('totalEmployees', 2)
            ->assertViewHas('totalVehicles', 2)
            ->assertViewHas('activePasses', 1)
            ->assertViewHas('expiredPasses', 1)
            ->assertViewHas('successfulScansToday', 1)
            ->assertViewHas('deniedScansToday', 1);

        $this->get(route('my-vehicles.index'))
            ->assertOk()
            ->assertSee('OWN-200')
            ->assertDontSee('OTHER-201');

        $this->get(route('vehicles.index', ['status' => 'Expired']))
            ->assertOk()
            ->assertSee('OTHER-201')
            ->assertDontSee('OWN-200');
    }

    private function createVehicle(CdoRecord $employee, array $overrides = []): Vehicle
    {
        return Vehicle::create(array_merge([
            'employee_id' => $employee->id,
            'plate_number' => 'TEST-'.$employee->id,
            'make' => 'Honda',
            'model' => 'City',
            'color' => 'Blue',
            'vehicle_type' => 'Sedan',
            'registration_date' => '2025-01-01',
            'expiration_date' => '2030-01-01',
            'status' => 'Active',
        ], $overrides));
    }
}
