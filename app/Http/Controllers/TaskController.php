<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Patient;
use App\Models\PatientTask;
use Illuminate\Http\Request;
use App\Models\DoctorPatient;
use Illuminate\Support\Facades\DB;
use App\http\Helpers\ResponseHelper;
use App\Http\Resources\TaskResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\PatientResource;

class TaskController extends Controller
{
   
    public function index()
    {
        $tasks = Task::all();
        // return response()->json(['tasks' => $tasks]);
        return TaskResource::collection($tasks);
    }

    public function store(Request $request)
    {
        $doctor_id = auth('doctor')->id();

        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:timer,multiple_choice,yes_no',
            'task_points' => 'required|integer',
            'target_date' => 'required|date',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.time_seconds' => 'required_if:type,timer|nullable|integer',
            'questions.*.options' => 'required_if:type,multiple_choice|array|nullable',
        ]);
        
        if ($request->has('patient_ids')) {
            $validPatients = DoctorPatient::whereIn('patient_id', $request->patient_ids)
                ->where('doctor_id', $doctor_id)
                ->pluck('id')
                ->toArray();

            $task = Task::create([
                'name' => $request->name,
                'type' => $request->type,
                'doctor_id' => $doctor_id,
                'task_points' => $request->task_points,
                'target_date' => $request->target_date
            ]);

            foreach ($request->questions as $q) {
                $question = $task->questions()->create([
                    'question_text' => $q['question_text'],
                    'time_seconds' => $request->type === 'timer' ? $q['time_seconds'] : null
                ]);

                if ($request->type === 'multiple_choice' && isset($q['options'])) {
                    foreach ($q['options'] as $optionText) {
                        $question->options()->create(['text' => $optionText]);
                    }
                }
            }

            $task->patients()->attach($validPatients);
        }

        return response()->json(['message' => 'Task created successfully']);
    }

    public function show($id)
    {
        $task = Task::with(['questions.options', 'patients'])->findOrFail($id);
        return new TaskResource($task);

    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->update([
            'name' => $request->name ?? $task->name,
            'task_points' => $request->task_points ?? $task->task_points,
            'target_date' => $request->target_date ?? $task->target_date,
        ]);

        if ($request->has('patient_ids')) {
            $validPatients = DoctorPatient::whereIn('patient_id', $request->patient_ids)
                ->where('doctor_id', $task->doctor_id)
                ->pluck('patient_id')
                ->toArray();

            $task->patients()->sync($validPatients); // تحديث المرفقين بدل تكرارهم
        }

        return response()->json(['message' => 'Task updated successfully']);
    }

    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        // حذف العلاقات قبل حذف التاسك نفسه
        $task->patients()->detach(); 
        foreach ($task->questions as $question) {
            $question->options()->delete(); 
            $question->delete();
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function markTaskInProgress($task_id)
    {
        $patient_id = auth('patient')->id();

        $patientTask = PatientTask::where('patient_id', $patient_id)
            ->where('task_id', $task_id)
            ->first();

        if (!$patientTask) {
            return response()->json(['error' => 'Task not found for this patient'], 404);
        }

        $patientTask->status = 'In Progress';
        $patientTask->save();

        return response()->json(['message' => 'Task status updated to In Progress']);
    }



}
