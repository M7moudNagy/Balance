<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Doctor extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use CanResetPassword;
    use Notifiable;
    protected $guarded = [];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'doctor'];
    }
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'doctor_patients')
                    ->withPivot('fullname', 'email', 'phoneNumber', 'age', 'typeOfAddiction','status','durationOfAddication')
                    ->withTimestamps();
    }    

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    public function sentMessages()
    {
        return $this->morphMany(Massage::class, 'sender');
    }

    public function receivedMessages()
    {
        return $this->morphMany(Massage::class, 'receiver');
    }
    public function statistics()
    {
        return $this->hasOne(DoctorStatistic::class, 'doctor_id');
    }
    public function posts()
    {
        return $this->morphMany(Post::class, 'user');
    }






}
