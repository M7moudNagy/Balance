<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorPatient;
use App\Models\DoctorStatistic;
use App\Models\Patient;

class DoctorPatientController extends Controller
{
    public function assignDoctorToPatient($doctor_id)
    {
        $user = auth('patient')->user();
        $patient_id = $user->id;
    
        $patient = Patient::find($patient_id);
        if (!$patient) {
            return response()->json(['message' => 'Patient not found or unauthorized'], 403);
        }
    
        $alreadyAssigned = $patient->doctors()->exists();
        if ($alreadyAssigned) {
            return response()->json(['message' => 'Patient is already assigned to a doctor.'], 409);
        }
    
        $patient->doctors()->sync([$doctor_id]);
    
        $doctorStatistic = DoctorStatistic::find($doctor_id);
        if ($doctorStatistic) {
            $doctorStatistic->updatePatientCount();
        }
    
        return response()->json(['message' => 'Doctor assigned successfully']);
    }
    

    public function getPatientDetailsForAssignment()
    {
        $user = auth('patient')->user();
        $check_assign = DoctorPatient::where('patient_id', 1)
        ->first();

        if (!$check_assign) {
        return response()->json(['message' => 'Doctor Does not assigned to this patient.'], 409);
        }
        $patient = Patient::where('id', 1)->first();

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
