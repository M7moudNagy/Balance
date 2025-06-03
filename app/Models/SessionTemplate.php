<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionTemplate extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['recurrence_days' => 'array'];

    public function doctor() {
        return $this->belongsTo(Doctor::class);
    }

    public function patient() {
        return $this->belongsTo(Patient::class);
    }
}
