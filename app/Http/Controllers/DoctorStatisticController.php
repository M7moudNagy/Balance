<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorStatisticController extends Controller
{
    public function incrementDoctorView($id)
    {
        $doctor = Doctor::findOrFail($id);
        $stats = $doctor->statistics()->firstOrCreate([]);
        $stats->increment('views');

        return response()->json(['message' => 'View incremented']);
    }

    public function rateDoctor(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $doctor = Doctor::findOrFail($id);
        $stats = $doctor->statistics()->firstOrCreate([]);

        $stats->increment('rating_total', $request->rating);
        $stats->increment('rating_count');

        if ($stats->rating_count > 0) {
            $stats->average_rating = round($stats->rating_total / $stats->rating_count, 1);
        } else {
            $stats->average_rating = 0;
        }

        $stats->save();

        return response()->json(['message' => 'Rating submitted']);
    }
    public function updateDoctorPatientCount($id)
    {
        $doctor = Doctor::withCount('patients')->findOrFail($id);

        $stats = $doctor->statistics()->firstOrCreate([]);
        $stats->patients_count = $doctor->patients_count;
        $stats->save();

        return response()->json([
            'message' => 'Patient count updated',
            'number_of_patients' => $doctor->patients_count
        ]);
    }
    public function getDoctorStatistics($id)
    {
        $doctor = Doctor::withCount('patients')->findOrFail($id);
        $stats = $doctor->statistics;
        return response()->json([
            'doctor_id' => $doctor->id,
            'average_rating' => $stats->average_rating,
            'rating_count' => $stats->rating_count ?? 0,
            'views' => $stats->views ?? 0,
            'number_of_patients' => $stats->number_of_patients ?? $doctor->patients_count,
        ]);
    }

}
