<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{
    return [
        "ID" => $this->id,
        "FullName" => $this->fullname,
        // "PhoneNumber" => $this->phone_number,
        // "Email" => $this->email,
        // "Gender" => $this->gender,
        "Specialization" => $this->specialization,
        // "Medical_License_Number" => $this->medical_license_number,
        "Years_of_Experience" => $this->years_of_experience,
        // "Clinic_Or_Hospital_Name" => $this->clinic_or_hospital_name,
        // "Work_Address" => $this->work_address,
        // "Available_Working_Hours" => $this->available_working_hours,
        "Image" => $this->image ? asset('storage/' . $this->image) : null,

        "Statistics" => [
        "NumberofPatients" => $this->statistics->patients_count ?? 0,  
        // "Rating Sum" => $this->statistics->rating_total ?? 0,  
        "AverageRating" => $this->statistics && $this->statistics->rating_count > 0
        ? round($this->statistics->rating_total / $this->statistics->rating_count, 2) 
        : 0,
        "Views" => $this->statistics->views ?? 0,
]
    ];
}

}
