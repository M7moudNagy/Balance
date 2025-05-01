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
            "ID"=> $this->id,
            "First Name"=> $this->first_name,
            "Last Name"=> $this->last_name,
            "Mobile Number"=> $this->mobile_number,
            "Date Of Birth"=> $this->date_of_birth,
            "Gender"=> $this->gender,
            "Address"=> $this->address,
            "Email"=> $this->email,
//        "governorate": "القاهرة",
//        "medical_specialty": "Cardiology",
//        "years_of_experience": "10",
//        "type_of_practice": "Private",
//        "facility_name": "Heart Care Clinic",
//        "facility_address": "456 شارع الثورة, الجيزة",
//        "facility_governorate": "الجيزة",
//        "medical_license_number": "ML123456789",
//        "medical_license": "uploads/doctors/NmcDdEmOZA0S5LveEs8vIbwciqUjA7x1dhmLnL4Z.jpg",
//        "graduation_certificate": "uploads/doctors/dDWxU6pNorqnmiFT1Ofep2P0mvBzEwPWVw02UuBD.jpg",
//        "national_id_or_passport": "uploads/doctors/7AlAsPWi3BGWEPzLOZGZ9MHfzRXynXW78znH3wfb.jpg",
//        "motivation": "أرغب في مساعدة المرضى وتحسين الرعاية الصحية.",
//        "balance_help": "أساهم في توعية المجتمع بأمراض القلب.",
//        "licensed_provider": true,
//        "agree_terms": true,
//        "updated_at": "2025-03-04T22:29:49.000000Z",
//        "Created At" => $this->created_at,
//        "ID": $this->id,
        ];
    }
}
