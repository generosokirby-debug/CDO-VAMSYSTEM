<?php

namespace App\Models;

use Database\Factories\CdoRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['employee_id', 'name', 'email', 'phone_number', 'department', 'position', 'status'])]
class CdoRecord extends Model
{
    /** @use HasFactory<CdoRecordFactory> */
    use HasFactory;

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'employee_id');
    }
}
