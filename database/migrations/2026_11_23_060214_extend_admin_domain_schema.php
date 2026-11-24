<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('Admin')->after('password');
            $table->string('status', 20)->default('Active')->after('role')->index();
            $table->foreignId('employee_record_id')->nullable()->constrained('cdo_records')->nullOnDelete();
        });

        Schema::table('cdo_records', function (Blueprint $table) {
            $table->string('employee_id', 40)->nullable()->unique()->after('id');
        });

        DB::table('cdo_records')->orderBy('id')->chunkById(100, function ($records): void {
            foreach ($records as $record) {
                DB::table('cdo_records')
                    ->where('id', $record->id)
                    ->update(['employee_id' => 'CDO-'.str_pad((string) $record->id, 6, '0', STR_PAD_LEFT)]);
            }
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('cdo_records')->restrictOnDelete();
            $table->string('plate_number', 20)->unique();
            $table->string('make', 80);
            $table->string('model', 80);
            $table->string('color', 50);
            $table->string('vehicle_type', 40)->index();
            $table->string('vin', 80)->nullable()->unique();
            $table->date('registration_date');
            $table->date('expiration_date')->index();
            $table->string('status', 20)->default('Active')->index();
            $table->string('current_pass_id', 64)->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('gates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('gate_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('cdo_records')->nullOnDelete();
            $table->foreignId('gate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('scan_result', 40)->index();
            $table->string('access_status', 20)->index();
            $table->string('plate_number', 20)->nullable()->index();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30);
            $table->string('module', 40);
            $table->boolean('allowed')->default(false);
            $table->timestamps();
            $table->unique(['role', 'module']);
        });

        Schema::create('user_permission_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('module', 40);
            $table->boolean('allowed');
            $table->timestamps();
            $table->unique(['user_id', 'module']);
        });

        $modules = ['dashboard', 'employees', 'vehicles', 'gate-scanner', 'logs', 'my-vehicles', 'settings'];
        $defaults = [
            'Super Admin' => $modules,
            'Admin' => $modules,
            'Guard' => ['gate-scanner'],
            'Employee' => ['my-vehicles'],
        ];

        foreach ($defaults as $role => $allowedModules) {
            foreach ($modules as $module) {
                DB::table('role_permissions')->insert([
                    'role' => $role,
                    'module' => $module,
                    'allowed' => in_array($module, $allowedModules, true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permission_overrides');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('gate_scans');
        Schema::dropIfExists('gates');
        Schema::dropIfExists('vehicles');

        Schema::table('cdo_records', function (Blueprint $table) {
            $table->dropUnique(['employee_id']);
            $table->dropColumn('employee_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_record_id');
            $table->dropIndex(['status']);
            $table->dropColumn(['role', 'status']);
        });
    }
};
