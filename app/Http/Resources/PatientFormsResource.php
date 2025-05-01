<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientFormsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'forms'      => FormResource::collection($this->whenLoaded('forms')),
            'patient'      => new PatientResource($this->whenLoaded('patient')),
        ];
    }
}
