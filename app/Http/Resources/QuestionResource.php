<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'question_text' => $this->question_text,
            'type' => $this->type,
            'time_seconds' => $this->time_seconds,
            'options' => OptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
