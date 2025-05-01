<?php

namespace App\Http\Controllers;

use App\Http\Resources\PatientResource;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'age' => 'required|numeric',
            'city' => 'required|string',
            'password' => 'required|min:6|confirmed',
            'gander' => 'required|string',
            'nickname' => 'required|string|max:40',
            'avatar' => 'nullable|string', // هنا هيكون اسم صورة فقط
        ]);

        // إنشاء المريض
        $patient = Patient::create([
            'fullname' => $request->fullname,
            'nickname'=> $request->nickname,
            'email' => $request->email,
            'age' => $request->age,
            'city' => $request->city,
            'password' => Hash::make($request->password),
            'gander' => $request->gander,
            'avatar' => $request->avatar, // اسم الصورة المختارة
        ]);

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
        $validatedData['password'] = Hash::make($request->password);
        $doctor = Doctor::create($validatedData);
        $doctorId = $doctor->id;

        $medicalLicensePath = $request->file('medical_license')->storeAs(
            'uploads/doctors/medical_licenses',
            'license_' . $doctorId . '.' . $request->file('medical_license')->extension(),
            'public');
        $graduationCertPath = $request->file('graduation_certificate')->storeAs(
            'uploads/doctors/graduation_certificates',
            'grad_' . $doctorId . '.' . $request->file('graduation_certificate')->extension(),
            'public');
        $nationalIdPath = $request->file('national_id_or_passport')->storeAs(
            'uploads/doctors/national_id_or_passports',
            'id_' . $doctorId . '.' . $request->file('national_id_or_passport')->extension(),
            'public');
        $imagePath = $request->file('image')->storeAs(
            'uploads/doctors/images',
            'doctor_' . $doctorId . '.' . $request->file('image')->extension(),
            'public');

        $doctor->update([
            'medical_license' => $medicalLicensePath,
            'graduation_certificate' => $graduationCertPath,
            'national_id_or_passport' => $nationalIdPath,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Doctor registered successfully',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

}
