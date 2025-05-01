<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Patient extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'patients';

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'patient'];
    }
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_patients');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'patients_tasks')
            ->withPivot('status')
            ->withTimestamps();
    }
    public function tips()
    {
        return $this->belongsToMany(Tip::class, 'patient_tip')
            ->withPivot('status')
            ->withTimestamps();
    }
    public function forms()
    {
        return $this->belongsToMany(Tip::class, 'patient_forms')
        ->withPivot('status')
        ->withTimestamps();
    }
    public function sentMessages()
    {
        return $this->morphMany(Massage::class, 'sender');
    }

    public function receivedMessages()
    {
        return $this->morphMany(Massage::class, 'receiver');
    }


}
