<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Models\Task;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientTask;
use Illuminate\Http\Request;
use App\Models\DoctorPatient;
use App\Models\DoctorStatistic;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\PatientResource;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function overdueTasks()
    {
        $patient_id = auth('patient')->id();

        $patient_tasks = PatientTask::where('patient_id', $patient_id)
            ->where('status', '!=', 'Completed')
            ->get();

        foreach ($patient_tasks as $patient_task) {
            $task = Task::find($patient_task->task_id);

            if ($task && $task->target_date < now()) {
                $patient_task->status = 'Overdue';
                $patient_task->save();
            }
        }

        return response()->json(['message' => 'Overdue tasks updated successfully']);
    }
    public function loginPatient(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = Auth::guard('patient')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $patient = Patient::where('email',$request->email)->first();

        $has_doctor = DoctorPatient::where('patient_id', $patient->id)->exists();

            if ($has_doctor) {
                $this->overduetasks();
                return response()->json([
                    'token' => $token,
                    'hasDoctor' => true,
                    'user' => new PatientResource(Auth::guard('patient')->user())
                ]);
            } else {
                return response()->json([
                    'token' => $token,
                    'hasDoctor' => false,
                    'user' => new PatientResource(Auth::guard('patient')->user())
                ]);
            }

    }
    public function registerPatient(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
            'phoneNumber' => 'required|string',
            'age' => 'required|numeric',
            'city' => 'required|string',
            'password' => 'required|min:6|confirmed',
            'gander' => 'required|string',
            'nickname' => 'required|string|max:40',
            'avatar' => 'nullable|integer',
        ]);

        $avatarName = $request->avatar ? $request->avatar . '.png' : '0.png';

        if (!Storage::exists('public/avatars/' . $avatarName)) {
            
            return response()->json(['message' => 'Invalid avatar selected'], 422);
        }

        $patient = Patient::create([
            'fullname' => $request->fullname,
            'nickname'=> $request->nickname,
            'email' => $request->email,
            'age' => $request->age,
            'city' => $request->city,
            'password' => Hash::make($request->password),
            'gander' => $request->gander,
            'avatar' => $avatarName,
            'phoneNumber' => $request->phoneNumber,

        ]);

        // $avatar_url = $request->avatar ? asset('storage/avatars/' . $request->avatar) : null;


        // تسجيل دخول تلقائي بعد التسجيل

        return response()->json([
            'message' => 'Patient registered successfully',

        ]);
    }
    public function loginDoctor(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = Auth::guard('doctor')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
            'hasDoctor' => true,
            'user' => new DoctorResource(Auth::guard('doctor')->user())
        ]);
    }
    public function registerDoctor(Request $request)
    {
        $validatedData = $request->validate([
            'fullname' => 'required|string',
            'phone_number' => 'required|string|unique:doctors,phone_number',
            'email' => 'required|email|unique:doctors,email',
            'password' => 'required|string|min:8|confirmed',
            'specialization' => 'required|string',
            'medical_license_number' => 'required|string|unique:doctors,medical_license_number',
            'years_of_experience' => 'required|integer',
            'clinic_or_hospital_name' => 'required|string',
            'work_address' => 'required|string',
            'available_working_hours' => 'required|string',
            'gender' => 'required|in:male,female,other',
            'image' => 'required|image|mimes:jpg,png,jpeg',
            'bio' => 'required|string|max:255',
        ]);
    
        $validatedData['password'] = Hash::make($request->password);
        $doctor = Doctor::create($validatedData);
        DoctorStatistic::create([
            'doctor_id' => $doctor->id,
            'rating_total' => 0,
            'rating_count' => 0,
            'views' => 0,
            'patients_count' => 0,
            'average_rating' => 0.0,
        ]);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs(
                'uploads/doctors/images',
                'doctor_' . $doctor->id . '.' . $request->file('image')->extension(),
                'public'
            );
            $doctor->update(['image' => $imagePath]);
        }
        return response()->json([
            'message' => 'Doctor registered successfully',
        ]);
    }
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }
}
