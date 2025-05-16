<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorPatient;
use App\Models\DoctorStatistic;
use App\Models\Patient;
use Illuminate\Http\Request;

class DoctorPatientController extends Controller
{
    public function assignDoctorToPatient(Request $request,$doctor_id)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'phoneNumber' => 'required|string',
            'age' => 'required|numeric',
            'typeOfAddiction' => 'required|string',
            'durationOfAddication' => 'required|string|max:7',
        ]);
        $patient_id = auth('patient')->id();
        try {
            $patient = Patient::findOrFail($patient_id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Patient not found'], 404);
        }
        
        $alreadyAssigned = $patient->doctors()->exists();
        if ($alreadyAssigned) {
            return response()->json(['message' => 'Patient is already assigned to a doctor.'], 409);
        }
    
        $patient->doctors()->attach($doctor_id, [
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phoneNumber' => $request->phoneNumber,
            'age' => $request->age,
            'typeOfAddiction' => $request->typeOfAddiction,
            'durationOfAddication' => $request->durationOfAddication,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $doctorStatistic = DoctorStatistic::where('doctor_id', $doctor_id)->first();
        if ($doctorStatistic) {
            $doctorStatistic->updateDoctorPatientCount();
            $doctorStatistic = $doctorStatistic->fresh(); 
        }

        return response()->json([
            'message' => 'Doctor assigned successfully',
            'doctor_id' => $doctor_id,
            // 'rating_total' => $doctorStatistic->rating_total,
            // 'views' => $doctorStatistic->views,
            // 'patients_count' => $doctorStatistic->patients_count,
            // 'average_rating' => $doctorStatistic->average_rating,
        ]);
    }
    
    public function getPatientDetailsForAssignment()
    {
        $user = auth('patient')->user();
        $check_assign = DoctorPatient::where('patient_id', $user->id)
        ->first();

        if (!$check_assign) {
        return response()->json(['message' => 'Doctor Does not assigned to this patient.'], 409);
        }
        $patient = Patient::where('id', $user->id)->first();

        if (!$patient) {
            return response()->json(['message' => 'Patient not found'], 404);
        }
        return response()->json([
            'doctorId'      => $check_assign->doctor_id,
            'fullname'      => $patient->fullname,
            'phone_number'  => $patient->phoneNumber,
            'email'         => $patient->email, 
            'age'           => $patient->age,
        ]);
    }
    public function unassignDoctorFromPatient($doctor_id)
    {
        $user = auth('patient')->user();
        $patient_id = $user->id;

        $patient = Patient::find($patient_id);
        if (!$patient) {
            return response()->json(['message' => 'Patient not found or unauthorized'], 403);
        }

        $isAssigned = DoctorPatient::where('doctor_id', $doctor_id)
            ->where('patient_id', $patient->id)
            ->exists();

        if (!$isAssigned) {
            return response()->json(['message' => 'Doctor is not assigned to this patient.'], 404);
        }

        $patient->doctors()->detach($doctor_id);

        $doctorStatistic = DoctorStatistic::find($doctor_id);
        if ($doctorStatistic) {
            $doctorStatistic->updatePatientCount();
        }

        return response()->json(['message' => 'Doctor unassigned successfully']);
    }
}
