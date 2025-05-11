<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             'ID'=> $this->id,
            'Fullname' => $this->fullname,
            'Nickname'=> $this->nickname,
            'PhoneNumber'=> $this->phoneNumber,
            'Email' => $this->email,
            'Age' => $this->age,
            'City' => $this->city,
            'Gander' => $this->gander,
            'Avatar' => Storage::url('avatars/' . $this->avatar), // توليد رابط الصورة
            //        "Email Verified At"=> $this->email_verified_at,
//        "Password"=> $this->password,
//        "Doctor ID"=> $this->doctor_id,
//        "Remember_token"=> $this->remember_token
        ];
    }
}
