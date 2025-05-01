<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopRatedDoctorsResource extends JsonResource
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
            'First Name'=> $this->first_name,
            'Last Name'=> $this->last_name,
            'Medical Specialty'=> $this->medical_specialty,
            'Rating'=> $this->rating,
        ];
    }
}
