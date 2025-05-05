<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\DoctorStatistic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\PatientResource;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function loginPatient(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$token = Auth::guard('patient')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
//            'user' => Auth::guard('patient')->user()
            'user' => new PatientResource(Auth::guard('patient')->user())
        ]);
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

        $avatarName = $request->avatar ? $request->avatar . '.png' : null;

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
//        $token = Auth::guard('patient')->login($patient);

        return response()->json([
            'message' => 'Patient registered successfully',

//            'user' => $patient,
//            'avatar_url' => $request->avatar ? asset('storage/' . $request->avatar) : null,
//            'token' => $token,
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
            'user' => Auth::guard('doctor')->user(),
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
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
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
            'doctor_id' => $doctor->id
        ]);
    }
    

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

}
