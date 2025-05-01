<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientTasksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'Patient' => [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'mobile_number' => $this->mobile_number,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'governorate' => $this->governorate,
                'address' => $this->address,
                'email' => $this->email,
                'doctor_id' => $this->doctor_id,
            ],
            'Task' => $this->tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->pivot->status, // إضافة الـ status هنا
                    'assign_date' => $task->assign_date,
                    'target_date' => $task->target_date,
                    'category_id' => $task->category_id,
                    "days" => json_decode($task->days), // استخدم الـ task هنا بدلاً من الـ patient
                    'notes' => $task->notes,
                    'doctor_id' => $task->doctor_id,
                ];
            }),
        ];
    }
}
