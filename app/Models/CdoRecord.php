<?php

namespace App\Models;

use Database\Factories\CdoRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone_number', 'department', 'position', 'status'])]
class CdoRecord extends Model
{
    /** @use HasFactory<CdoRecordFactory> */
    use HasFactory;
}
