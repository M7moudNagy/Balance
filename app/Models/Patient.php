<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
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
        return $this->belongsToMany(Doctor::class, 'doctor_patients', 'patient_id', 'doctor_id')
        ->withPivot('fullname', 'email', 'phoneNumber', 'age', 'typeOfAddiction','status','durationOfAddication')
                    ->withTimestamps();
    }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'patients_tasks')
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
    public function posts()
    {
        return $this->morphMany(Post::class, 'user');
    }
    public function challengs()
    {
        return $this->morphMany(Challenge::class, 'user');
    }
}
