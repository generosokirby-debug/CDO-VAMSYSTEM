<?php

namespace Tests\Feature;

use App\Models\CdoRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CdoRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitors_can_view_the_dashboard_without_signing_in(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Demo visitor');
    }

    public function test_visitors_can_search_cdo_records(): void
    {
        $matchingRecord = CdoRecord::factory()->create([
            'name' => 'Ada Sample',
            'department' => 'Operations',
        ]);
        CdoRecord::factory()->create([
            'name' => 'Grace Sample',
            'department' => 'Planning',
        ]);

        $this->get(route('dashboard', ['search' => 'Operations']))
            ->assertOk()
            ->assertSee($matchingRecord->name)
            ->assertDontSee('Grace Sample');
    }

    public function test_visitors_can_create_cdo_records(): void
    {
        $this->post(route('cdos.store'), [
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
        $this->post(route('cdos.store'), [
            'name' => 'Alex2',
            'email' => 'alex@example.test',
            'phone_number' => '09171234567',
            'department' => 'Oper@tions',
            'position' => 'Coordinator1',
            'status' => 'Active',
        ])
            ->assertSessionHasErrors([
                'name' => 'The name may only contain letters and spaces.',
                'email' => 'The email address must end with @gmail.com.',
                'department' => 'The department may only contain letters and spaces.',
                'position' => 'The position may only contain letters and spaces.',
            ]);

        $this->assertDatabaseCount('cdo_records', 0);
    }

    public function test_phone_numbers_must_contain_eleven_digits(): void
    {
        $this->post(route('cdos.store'), [
            'name' => 'Alex Sample',
            'email' => 'alex@gmail.com',
            'phone_number' => '0917123456',
            'department' => 'Operations',
            'position' => 'Coordinator',
            'status' => 'Active',
        ])
            ->assertSessionHasErrors([
                'phone_number' => 'The phone number must be an 11-digit Philippine mobile number starting with 09.',
            ]);

        $this->assertDatabaseCount('cdo_records', 0);
    }

    public function test_phone_numbers_must_start_with_the_philippine_mobile_prefix(): void
    {
        $this->post(route('cdos.store'), [
            'name' => 'Alex Sample',
            'email' => 'alex@gmail.com',
            'phone_number' => '12345678901',
            'department' => 'Operations',
            'position' => 'Coordinator',
            'status' => 'Active',
        ])
            ->assertSessionHasErrors([
                'phone_number' => 'The phone number must be an 11-digit Philippine mobile number starting with 09.',
            ]);

        $this->assertDatabaseCount('cdo_records', 0);
    }

    public function test_visitors_can_update_cdo_records(): void
    {
        $record = CdoRecord::factory()->create([
            'name' => 'Old Sample',
            'email' => 'old@gmail.com',
        ]);

        $this->put(route('cdos.update', $record), [
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

    public function test_visitors_can_delete_cdo_records(): void
    {
        $record = CdoRecord::factory()->create();

        $this->delete(route('cdos.destroy', $record))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'CDO record deleted.');

        $this->assertModelMissing($record);
    }
}
