<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'FirstName'=> $this->first_name,
            'LastName'=> $this->last_name,
            'MedicalSpecialty'=> $this->medical_specialty,
            'Rating'=> $this->rating,
            'Image' => Storage::url($this->image),        
        ];
    }
}
