<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\QuestionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'name' => $this->name,
            'doctor_id' => $this->doctor_id,
            'task_points' => $this->task_points,
            'target_date' => $this->target_date,
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'patient' => $this->patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'fullname' => $patient->fullname,
                    'nickname' => $patient->nickname,
                    'age' => $patient->age,
                    'phoneNumber' => $patient->phoneNumber,
                    'gander' => $patient->gander,
                    'city' => $patient->city,
                    'points' => $patient->points,
                    'email' => $patient->email,
                    'avatar' => $patient->avatar,
                ];
            }),
        ];
    }
}
