<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'days' => 'array',
    ];
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patients_tasks')
            ->withPivot('status')
            ->withTimestamps();
    }
    public function questions() {
        return $this->hasMany(Question::class);
    }
    
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
