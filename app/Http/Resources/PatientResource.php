<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

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
            'Email' => $this->email,
            'Age' => $this->age,
            'City' => $this->city,
            'Gander' => $this->gander,
            'Avatar' => $this->avatar,
//        "Email Verified At"=> $this->email_verified_at,
//        "Password"=> $this->password,
//        "Doctor ID"=> $this->doctor_id,
//        "Remember_token"=> $this->remember_token
        ];
    }
}
