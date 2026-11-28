<?php

namespace Tests\Feature;

use App\Models\CdoRecord;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CdoRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_view_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Overview');
    }

    public function test_admins_can_search_employees(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $matchingRecord = CdoRecord::factory()->create([
            'name' => 'Ada Sample',
            'department' => 'Operations',
        ]);
        CdoRecord::factory()->create([
            'name' => 'Grace Sample',
            'department' => 'Planning',
        ]);

        $this->get(route('employees.index', ['search' => 'Operations']))
            ->assertOk()
            ->assertSee($matchingRecord->name)
            ->assertDontSee('Grace Sample');
    }

    public function test_admins_can_create_cdo_records(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->post(route('cdos.store'), [
            'employee_id' => 'EMP-100',
            'name' => 'Alex Sample',
            'email' => 'alex@gmail.com',
            'phone_number' => '09171234567',
            'department' => 'Operations',
            'position' => 'Coordinator',
            'status' => 'Active',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'CDO record created.');

        $record = CdoRecord::where('email', 'alex@gmail.com')->firstOrFail();

        $this->assertModelExists($record);
        $this->assertSame('09171234567', $record->phone_number);
    }

    public function test_invalid_cdo_records_are_not_created(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->post(route('cdos.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'department' => '',
            'position' => '',
            'status' => 'Unknown',
        ])
            ->assertSessionHasErrors(['name', 'email', 'department', 'position', 'status']);

        $this->assertDatabaseCount('cdo_records', 0);
    }

    public function test_cdo_records_reject_non_letter_fields_and_non_gmail_addresses(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->post(route('cdos.store'), [
            'name' => 'Alex2',
            'email' => 'alex@example.test',
            'phone_number' => '09171234567',
            'department' => 'Oper@tions',
            'position' => 'Coordinator1',
            'status' => 'Active',
        ])
            ->assertSessionHasErrors([
                'name' => 'Use letters and spaces only.',
                'email' => 'Use a Gmail address ending in @gmail.com.',
                'department' => 'Use letters and spaces only.',
                'position' => 'Use letters and spaces only.',
            ]);

        $this->assertDatabaseCount('cdo_records', 0);
    }

    public function test_phone_numbers_must_contain_eleven_digits(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->post(route('cdos.store'), [
            'name' => 'Alex Sample',
            'email' => 'alex@gmail.com',
            'phone_number' => '0917123456',
            'department' => 'Operations',
            'position' => 'Coordinator',
            'status' => 'Active',
        ])
            ->assertSessionHasErrors([
                'phone_number' => 'Use an 11-digit mobile number starting with 09.',
            ]);

        $this->assertDatabaseCount('cdo_records', 0);
    }

    public function test_phone_numbers_must_start_with_the_philippine_mobile_prefix(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $this->post(route('cdos.store'), [
            'name' => 'Alex Sample',
            'email' => 'alex@gmail.com',
            'phone_number' => '12345678901',
            'department' => 'Operations',
            'position' => 'Coordinator',
            'status' => 'Active',
        ])
            ->assertSessionHasErrors([
                'phone_number' => 'Use an 11-digit mobile number starting with 09.',
            ]);

        $this->assertDatabaseCount('cdo_records', 0);
    }

    public function test_admins_can_update_cdo_records(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $record = CdoRecord::factory()->create([
            'name' => 'Old Sample',
            'email' => 'old@gmail.com',
        ]);

        $this->put(route('cdos.update', $record), [
            'employee_id' => $record->employee_id,
            'name' => 'Updated Sample',
            'email' => $record->email,
            'phone_number' => '09181234567',
            'department' => 'Planning',
            'position' => 'Lead',
            'status' => 'Inactive',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'CDO record updated.');

        $this->assertDatabaseHas('cdo_records', [
            'id' => $record->id,
            'name' => 'Updated Sample',
            'department' => 'Planning',
            'position' => 'Lead',
            'phone_number' => '09181234567',
            'status' => 'Inactive',
        ]);
    }

    public function test_admins_can_delete_cdo_records(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $record = CdoRecord::factory()->create();

        $this->delete(route('cdos.destroy', $record))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'CDO record deleted.');

        $this->assertModelMissing($record);
    }

    public function test_cdo_records_with_assigned_vehicles_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'Admin']));
        $record = CdoRecord::factory()->create();
        $vehicle = Vehicle::create([
            'employee_id' => $record->id,
            'plate_number' => 'CDO-TEST-1',
            'make' => 'Honda',
            'model' => 'City',
            'color' => 'Blue',
            'vehicle_type' => 'Sedan',
            'registration_date' => '2025-01-01',
            'expiration_date' => '2030-01-01',
            'status' => 'Active',
        ]);

        $this->from(route('dashboard'))
            ->delete(route('cdos.destroy', $record))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors(['record' => 'Remove assigned vehicles first.']);

        $this->assertModelExists($record);
        $this->assertModelExists($vehicle);
    }
}
