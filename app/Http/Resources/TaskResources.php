<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "Task ID"     => $this->id,
            "Title"       => $this->title,
            "Description" => $this->description,
            'category'    => new CategoryResource($this->whenLoaded('category')),
            "assign_date" => $this->assign_date,
            "target_date" => $this->target_date,
            "repeat"      => $this->repeat,
            "days" => json_decode($this->days),
            "notes"       => $this->notes,
            "doctor_id"   => $this->doctor_id,
            'patients' => PatientResource::collection($this->whenLoaded('patients')),

        ];
    }
}
