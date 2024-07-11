<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class JobVacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
        'salary',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
    ];

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->utc()->format('Y-m-d H:i:s');
    }
}
