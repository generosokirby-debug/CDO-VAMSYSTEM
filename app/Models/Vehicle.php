<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'employee_id',
    'plate_number',
    'make',
    'model',
    'color',
    'vehicle_type',
    'vin',
    'registration_date',
    'expiration_date',
    'status',
    'current_pass_id',
])]
class Vehicle extends Model
{
    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'expiration_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(CdoRecord::class, 'employee_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(GateScan::class);
    }
}
