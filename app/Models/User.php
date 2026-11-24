<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'employee_record_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var array<string, bool>|null
     */
    private ?array $moduleAccessCache = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employeeRecord(): BelongsTo
    {
        return $this->belongsTo(CdoRecord::class, 'employee_record_id');
    }

    public function hasAdminAccess(): bool
    {
        return $this->status === 'Active' && in_array($this->role, ['Admin', 'Super Admin'], true);
    }

    public function hasModuleAccess(string $module): bool
    {
        if ($this->status !== 'Active') {
            return false;
        }

        if ($this->role === 'Super Admin') {
            return true;
        }

        if ($this->moduleAccessCache === null) {
            $this->moduleAccessCache = DB::table('role_permissions')
                ->where('role', $this->role)
                ->pluck('allowed', 'module')
                ->map(fn (mixed $allowed): bool => (bool) $allowed)
                ->all();

            DB::table('user_permission_overrides')
                ->where('user_id', $this->id)
                ->pluck('allowed', 'module')
                ->each(function (mixed $allowed, string $module): void {
                    $this->moduleAccessCache[$module] = (bool) $allowed;
                });
        }

        return $this->moduleAccessCache[$module] ?? false;
    }
}
