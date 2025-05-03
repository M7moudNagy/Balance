<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorPatientController extends Controller
{
    public function assignDoctorToPatient(Request $request)
{
    $request->validate([
        'doctor_id' => 'required|exists:doctors,id',
    ]);
    $patient = auth()->user();
    if (!$patient instanceof \App\Models\Patient) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    $patient->doctors()->syncWithoutDetaching([$request->doctor_id]);
    $doctor = \App\Models\Doctor::findOrFail($request->doctor_id);
    $doctor->updatePatientCount();
    return response()->json(['message' => 'Doctor assigned successfully']);
}

    

}
