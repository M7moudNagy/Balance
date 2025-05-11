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
        // dd($this->statistic, $this->statistic->average_rating);

        return [
            'ID' => $this->id,
            'fullname' => $this->fullname,
            'specialization' => $this->specialization,
            'Rating' => optional($this->statistic)->average_rating ?? 0,
            'Image' => Storage::url($this->image),
        ];
    }
}
