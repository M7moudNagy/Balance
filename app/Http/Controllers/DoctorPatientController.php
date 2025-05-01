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

        // ربط المريض بالدكتور
//        $patient->doctors()->attach($request->doctor_id);
        $patient->doctors()->syncWithoutDetaching([$request->doctor_id]);

        return response()->json(['message' => 'Doctor assigned successfully']);
    }
    

}
