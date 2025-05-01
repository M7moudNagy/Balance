<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "Form ID"=> $this->id ,
            "Title"=> $this->title,
            "Description"=> $this->description,
            "assign_date"=> $this->assign_date,
            "target_date"=> $this->target_date,
            "repeat"=> $this->repeat,
            "notes"=> $this->notes,
            "doctor_id"=> $this->doctor_id,
            'patients' => PatientResource::collection($this->whenLoaded('patients')),

        ];
    }
}
