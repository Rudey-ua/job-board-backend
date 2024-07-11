<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class JobVacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'salary',
    ];

    protected $hidden = [
        'created_at',
    ];

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->utc()->format('Y-m-d H:i:s');
    }
}
