<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\PatientTasksResource;
use App\Http\Resources\TopRatedDoctorsResource;
use App\Models\Form;
use App\Models\Patient;
use App\Models\PatientForm;
use App\Models\PatientTask;
use App\Models\PatientTip;
use App\Models\Task;
use App\Models\Tip;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\DoctorStatistic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Collection;

class DoctorController extends Controller
{
    public function index()
{
    return DoctorResource::collection(
        Doctor::with('statistics')->get()
    );
}


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:doctors,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'governorate' => 'required|string',
            'medical_specialty' => 'required|string',
            'years_of_experience' => 'required|integer',
            'type_of_practice' => 'required|string',
            'facility_name' => 'nullable|string',
            'facility_address' => 'nullable|string',
            'facility_governorate' => 'nullable|string',
            'medical_license_number' => 'required|string|unique:doctors,medical_license_number',
            'medical_license' => 'required|file|mimes:pdf,jpg,png',
            'graduation_certificate' => 'required|file|mimes:pdf,jpg,png',
            'national_id_or_passport' => 'required|file|mimes:pdf,jpg,png',
            'other_certifications' => 'nullable|json',
            'motivation' => 'required|string',
            'balance_help' => 'required|string',
            'licensed_provider' => 'required|boolean',
            'agree_terms' => 'required|boolean',
            'image' => 'required|image|mimes:jpg,png,jpeg', // ✅ صورة الدكتور
        ]);

        $validatedData['medical_license'] = $request->file('medical_license')->store('uploads/doctors/medical_licenses', 'public');
        $validatedData['graduation_certificate'] = $request->file('graduation_certificate')->store('uploads/doctors/graduation_certificates', 'public');
        $validatedData['national_id_or_passport'] = $request->file('national_id_or_passport')->store('uploads/doctors/national_id_or_passports', 'public');
        $validatedData['image'] = $request->file('image')->store('uploads/doctors/images', 'public');

        $doctor = Doctor::create($validatedData);

        return response()->json([
            'message' => 'Doctor registered successfully',
        ]);
        // أو تستخدم: return new DoctorResource($doctor);
    }


    public function show($id)
    {
        $doctor = Doctor::findorfail('id',$id)->first();
        return new DoctorResource($doctor);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validatedData = $request->validate([
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:doctors,email,' . $doctor->id,
            'phone_number' => 'sometimes|string',
            'gender' => 'sometimes|in:male,female,other',
            'date_of_birth' => 'sometimes|date',
            'address' => 'sometimes|string',
            'governorate' => 'sometimes|string',
            'medical_specialty' => 'sometimes|string',
            'years_of_experience' => 'sometimes|integer',
            'type_of_practice' => 'sometimes|string',
            'facility_name' => 'nullable|string',
            'facility_address' => 'nullable|string',
            'facility_governorate' => 'nullable|string',
            'medical_license_number' => 'sometimes|string|unique:doctors,medical_license_number,' . $doctor->id,
            'medical_license' => 'sometimes|file|mimes:pdf,jpg,png|max:2048',
            'graduation_certificate' => 'sometimes|file|mimes:pdf,jpg,png|max:2048',
            'national_id_or_passport' => 'sometimes|file|mimes:pdf,jpg,png|max:2048',
            'other_certifications' => 'nullable|json',
            'motivation' => 'sometimes|string',
            'balance_help' => 'sometimes|string',
            'licensed_provider' => 'sometimes|boolean',
            'agree_terms' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('medical_license')) {
            Storage::delete($doctor->medical_license);
            $validatedData['medical_license'] = $request->file('medical_license')->store('uploads/doctors');
        }
        if ($request->hasFile('graduation_certificate')) {
            Storage::delete($doctor->graduation_certificate);
            $validatedData['graduation_certificate'] = $request->file('graduation_certificate')->store('uploads/doctors');
        }
        if ($request->hasFile('national_id_or_passport')) {
            Storage::delete($doctor->national_id_or_passport);
            $validatedData['national_id_or_passport'] = $request->file('national_id_or_passport')->store('uploads/doctors');
        }

        $doctor->update($validatedData);

        return new DoctorResource($doctor);
    }

    public function destroy($id)
    {
        $doctor = Doctor::findorfail('id',$id);
        if($doctor) {
            Storage::delete([$doctor->medical_license, $doctor->graduation_certificate, $doctor->national_id_or_passport]);
            $doctor->delete();
            return new DoctorResource(['doctor deleted successfully.']);
        }
        return new DoctorResource(['doctor not found.']);

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
                    'title' => $pt->task->title ?? null,
                    'description' => $pt->task->description ?? null,
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
                    'title' => $pt->task->title ?? null,
                    'description' => $pt->task->description ?? null,
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
                    'title' => $pt->task->title ?? null,
                    'description' => $pt->task->description ?? null,
                    'status' => $pt->status,
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
                    'title' => $pt->task->title ?? null,
                    'description' => $pt->task->description ?? null,
                    'status' => $pt->status,
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
            'status' => 'required|in:Under Treatment,Partial Recovery,Full Recovery'
        ]);
        
        $doctor->patients()->updateExistingPivot($patient_id, [
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Status updated successfully.']);
    }

    public function my_patients_tasks($id)
    {
        $patient = Patient::with(['tasks' => function($q) {
            $q->withPivot('status'); // إضافة الـ status من جدول الـ pivot
        }])->find($id);

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }

        return new PatientTasksResource($patient);
    }

    public function my_patients_forms($id)
    {
        $forms = PatientForm::where('patient_id',$id)->get();
        $Patientform = Form::whereIn('id', $forms->pluck('form_id'))->get();
        $patient = Patient::where('id',$id)->get();
        foreach ($patient as $p) {
        if($Patientform->isEmpty()){
            return response()->json(["status"=>"200","massage"=>["Your Patient"." ".$p->first_name." ".$p->last_name." Tips is empty"]]);
        }}
        return response()->json(["Patient"=>$patient,"Forms"=>$Patientform]);
//        return new PatientTasksResource($tasks);
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
