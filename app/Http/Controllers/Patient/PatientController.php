<?php

namespace App\Http\Controllers\Patient;

use App\Models\Tip;
use App\Models\Task;
use App\Models\Patient;
use App\Models\PatientTask;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\PatientResource;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{

    public function show($id)
    {
        $patient = Patient::findOrFail($id); 

        return new PatientResource($patient);
    }
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $data = $request->validate([
            'fullname' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:patients,email,' . $id,
            'phoneNumber' => 'sometimes|required|string|unique:patients,phoneNumber,' . $id,
            'age' => 'sometimes|required|numeric',
            'city' => 'sometimes|required|string',
            'password' => 'sometimes|nullable|min:6|confirmed',
            'gander' => 'sometimes|required|string',
            'nickname' => 'sometimes|required|string|max:40',
            'avatar' => 'nullable|integer',
        ]);

        if ($request->has('password') && $request->filled('password')) {
            $data['password'] = Hash::make($request->password); 
        }

        if ($request->has('avatar')) {
            $avatarName = $request->avatar . '.png';
            if (!Storage::exists('public/avatars/' . $avatarName)) {
                return response()->json(['message' => 'Invalid avatar selected'], 422);
            }
            $data['avatar'] = $avatarName;
        }

        $patient->update($data);

        return new PatientResource($patient);
    }
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        $doctor = $patient->doctors()->first();
        if ($doctor) {
            $patient->doctors()->detach($doctor->id);
            $doctor->updatePatientCount();
        }

        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }
    public function consecutiveCommitmentDays($patientId)
    {
        $completedTasks = PatientTask::where('patient_id', $patientId)
                                    ->where('status', 'completed') 
                                    ->orderBy('updated_at', 'asc') 
                                    ->get();

        // إذا ما فيش مهام مكتملة، إرجاع 0
        if ($completedTasks->isEmpty()) {
            return 0;
        }

        $consecutiveDays = 1; // البداية من يوم واحد كأول يوم ملتزم
        $previousTaskDate = null;

        foreach ($completedTasks as $task) {
            $taskDate = $task->updated_at->toDateString(); // تاريخ المهمة المُكتملة (يفضل استخدام التاريخ المحدَّث)

            // إذا كان التاريخ الحالي للمهمة يختلف عن التاريخ السابق، نعتبرها يوم جديد
            if ($previousTaskDate && $previousTaskDate != $taskDate) {
                // إذا الفرق يوم واحد
                if (now()->subDay()->toDateString() == $taskDate) {
                    $consecutiveDays++;
                } else {
                    break; // لو مش يوم متتالي، خلصنا الحساب
                }
            }

            $previousTaskDate = $taskDate;
        }

        return $consecutiveDays;
    }
    public function index($patientId)
    {
        $tasks = PatientTask::where('patient_id', $patientId)->get();

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'Completed')->count();

        $completionPercentage = 0;
        if ($totalTasks > 0) {
            $completionPercentage = ($completedTasks / $totalTasks) * 100;
        }

        $consecutiveDays = $this->consecutiveCommitmentDays($patientId);

        $pendingTasks = PatientTask::with('task')
            ->where('patient_id', $patientId)
            ->where('status', 'Pending')
            ->get()
            ->map(function ($pt) {
                return [
                    'task_id' => $pt->task_id,
                    'name' => $pt->task->name ?? null,
                    'task_points' => $pt->task->task_points ?? null,
                    'target_date' => $pt->target_date,
                ];
            });

        $inprogressTasks = PatientTask::with('task')
            ->where('patient_id', $patientId)
            ->where('status', 'In Progress')
            ->get()
            ->map(function ($pt) {
                return [
                    'task_id' => $pt->task_id,
                    'name' => $pt->task->name ?? null,
                    'task_points' => $pt->task->task_points ?? null,
                    'target_date' => $pt->target_date,
                ];
            });

        $completedTasksData = PatientTask::with('task')
            ->where('patient_id', $patientId)
            ->where('status', 'Completed')
            ->get()
            ->map(function ($pt) {
                return [
                    'task_id' => $pt->task_id,
                    'name' => $pt->task->name ?? null,
                    'task_points' => $pt->task->task_points ?? null,
                    'target_date' => $pt->target_date,
                    'completed_at' => $pt->completed_at,
                ];
            });

        $overdueTasks = PatientTask::with('task')
            ->where('patient_id', $patientId)
            ->where('status', 'Overdue')
            ->get()
            ->map(function ($pt) {
                return [
                    'task_id' => $pt->task_id,
                    'name' => $pt->task->name ?? null,
                    'task_points' => $pt->task->task_points ?? null,
                    'target_date' => $pt->target_date,
                ];
            });

        return response()->json([
            'patient_id' => $patientId,
            'completion_percentage' => $completionPercentage,
            'consecutive_commitment_days' => $consecutiveDays,
            'pendingTasks' => $pendingTasks,
            'inprogressTasks' => $inprogressTasks,
            'completedTasks' => $completedTasksData,
            'overdueTasks' => $overdueTasks,
        ]);
    }
}   