<?php

namespace App\Http\Controllers\Doctor;

use App\Models\Tip;
use App\Models\Form;
use App\Models\Task;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientTip;
use App\Models\PatientForm;
use App\Models\PatientTask;
use Illuminate\Http\Request;
use mysql_xdevapi\Collection;
use App\Models\DoctorStatistic;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\DoctorResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PatientTasksResource;
use App\Http\Resources\TopRatedDoctorsResource;

class DoctorController extends Controller
{
    public function index()
    {
        return DoctorResource::collection(
            Doctor::with('statistics')->get()
        );
    }
    public function show($id)
    {
        $doctor = Doctor::findorfail('id',$id)->first();
        return new DoctorResource($doctor);
    }

    public function updateDoctor(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $validatedData = $request->validate([
            'fullname' => 'sometimes|required|string',
            'phone_number' => 'sometimes|required|string|unique:doctors,phone_number,' . $doctor->id,
            'email' => 'sometimes|required|email|unique:doctors,email,' . $doctor->id,
            'password' => 'nullable|string|min:8|confirmed',
            'specialization' => 'sometimes|required|string',
            'medical_license_number' => 'sometimes|required|string|unique:doctors,medical_license_number,' . $doctor->id,
            'years_of_experience' => 'sometimes|required|integer',
            'clinic_or_hospital_name' => 'sometimes|required|string',
            'work_address' => 'sometimes|required|string',
            'available_working_hours' => 'sometimes|required|string',
            'gender' => 'sometimes|required|in:male,female,other',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
        ]);

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        $doctor->update($validatedData);

        if ($request->hasFile('image')) {
            if ($doctor->image) {
                Storage::disk('public')->delete($doctor->image);
            }

            $imagePath = $request->file('image')->storeAs(
                'uploads/doctors/images',
                'doctor_' . $doctor->id . '.' . $request->file('image')->extension(),
                'public'
            );

            $doctor->update(['image' => $imagePath]);
        }

        return response()->json([
            'message' => 'Doctor updated successfully',
            'doctor' => $doctor,
        ]);
    }

    public function deleteDoctor($id)
    {
        $doctor = Doctor::findOrFail($id);

        if ($doctor->image) {
            Storage::disk('public')->delete($doctor->image);
        }
        $doctor->delete();

        return response()->json([
            'message' => 'Doctor deleted successfully',
        ]);
    }

    public function my_patients()
    {
        $doctor = auth('doctor')->user();

        $patients = $doctor->patients->map(function ($patient) {
            return [
                'fullname' => $patient->pivot->fullname,
                'age' => $patient->pivot->age,
                'addictionType' => $patient->pivot->typeOfAddiction,
                'gender' => $patient->gander,
                'status' => $patient->pivot->status,

            ];
        });

        return response()->json([
            'patients' => $patients
        ]);
    }
 
    public function getPatientById($patient_id)
    {
        $doctor_id = auth('doctor')->id();
        $doctor = Doctor::findOrFail($doctor_id);
        $patient = $doctor->patients()->where('patients.id', $patient_id)->first();

        if (!$patient) {
            return response()->json(['message' => 'Patient not found or not assigned to this doctor'], 404);
        }
        $pendingTasks = PatientTask::with('task')
            ->where('patient_id', $patient_id)
            ->where('status', 'Pending')
            ->get()
            ->map(function ($pt) {
                return [
                    'task_id' => $pt->task_id,
                    'name' => $pt->task->name ?? null,
                    'task_points' => $pt->task->task_points ?? null,
                    'target_date' => $pt->task->target_date,
                    'status' => $pt->status,
                ];
            });

        $inprogressTasks = PatientTask::with('task')
            ->where('patient_id', $patient_id)
            ->where('status', 'In Progress')
            ->get()
            ->map(function ($pt) {
                return [
                    'task_id' => $pt->task_id,
                    'name' => $pt->task->name ?? null,
                    'task_points' => $pt->task->task_points ?? null,
                    'target_date' => $pt->target_date,
                    'status' => $pt->status,
                ];
            });

        $completedTasksData = PatientTask::with('task')
            ->where('patient_id', $patient_id)
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
            ->where('patient_id', $patient_id)
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
            'patient' => [
                'fullname' => $patient->pivot->fullname,
                'nickname' => $patient->nickname,
                'age' => $patient->pivot->age,
                'city' => $patient->city,
                'gender' => $patient->gander,
                'addictionType' => $patient->pivot->typeOfAddiction,
                'durationOfAddication' => $patient->pivot->durationOfAddication,
                'startDateOfTreatment' => $patient->pivot->created_at ? $patient->pivot->created_at->toDateTimeString() : null,
                'status' => $patient->pivot->status,
                'pendingTasks' => $pendingTasks,
                'inprogressTasks' => $inprogressTasks,
                'completedTasks' => $completedTasksData,
                'overdueTasks' => $overdueTasks,
            ]
        ]);
    }

    public function updatePatientStatus(Request $request, $patient_id)
    {
        $doctor_id = auth('doctor')->id();
        $doctor = Doctor::findOrFail($doctor_id);

        $request->validate([
            'status' => 'required|in:Partial Recovery,Full Recovery'
        ]);
        
        $doctor->patients()->updateExistingPivot($patient_id, [
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function my_patients_tasks($patient_id)
    {
        $doctor_id = auth('doctor')->id();

        $patient = Patient::find($patient_id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        $tasks = $patient->tasks()->where('patient_id', $patient_id)->withPivot('status')->get();

        return response()->json([
            'patient' => $patient->fullname,
            'tasks' => $tasks->map(function($task) {
                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'task_points' => $task->task_points,
                    'target_date' => $task->target_date,
                    'status' => $task->pivot->status,
                ];
            }),
        ]);
    }

    public function myPatientsTasks()
    {
        $doctor_id = auth('doctor')->id();

        // نجيب كل المرضى المرتبطين بالدكتور
        $patients = Patient::whereHas('doctors', function($q) use ($doctor_id) {
            $q->where('doctor_id', $doctor_id);
        })->with(['tasks' => function($q) {
            $q->withPivot('status');
        }])->get();

        return response()->json([
            'patients' => $patients
        ]);
    }


    public function getDoctorSummary($doctorId)
    {
        // المرضى المربوطين بالدكتور ده
        $patients = DB::table('patients')
            ->where('doctor_id', $doctorId)
            ->pluck('id')
            ->toArray();

        $patientsCount = count($patients);

        // المهام الخاصة بالمرضى دول
        $totalAssignedTasks = DB::table('patients_tasks')
            ->whereIn('patient_id', $patients)
            ->count();

        $statuses = DB::table('patients_tasks')
            ->select('status', DB::raw('count(*) as count'))
            ->whereIn('patient_id', $patients)
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $completed = $statuses['completed'] ?? 0;
        $inProgress = $statuses['in_progress'] ?? 0;
        $pending = $statuses['pending'] ?? 0;
        $overdue = $statuses['overdue'] ?? 0;

        $completionRate = $totalAssignedTasks > 0 ? round(($completed / $totalAssignedTasks) * 100, 2) : 0;

        $progressOverTime = DB::table('patients_tasks')
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as tasks_completed')
            ->whereIn('patient_id', $patients)
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'completion_rate' => $completionRate,
            'patients_assigned' => $patientsCount,
            'completion_status' => [
                'completed' => $totalAssignedTasks > 0 ? round(($completed / $totalAssignedTasks) * 100, 2) : 0,
                'in_progress' => $totalAssignedTasks > 0 ? round(($inProgress / $totalAssignedTasks) * 100, 2) : 0,
                'pending' => $totalAssignedTasks > 0 ? round(($pending / $totalAssignedTasks) * 100, 2) : 0,
                'overdue' => $totalAssignedTasks > 0 ? round(($overdue / $totalAssignedTasks) * 100, 2) : 0,
            ],
            'progress_over_time' => $progressOverTime
        ]);
    }

    public function top_rated_doctors()
    {
        $doctors = Doctor::with('statistics')
        ->whereHas('statistics', function ($query) {
            $query->where('average_rating', '>', 4.8);
        })
        ->get()
        ->sortByDesc(fn ($doctor) => $doctor->statistics->average_rating ?? 0)
        ->values();

        return TopRatedDoctorsResource::collection($doctors);
    }


    
}
