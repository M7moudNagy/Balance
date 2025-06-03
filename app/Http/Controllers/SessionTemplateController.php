<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;
use App\Models\SessionTemplate;

class SessionTemplateController extends Controller
{
    public function show($id) {
        $session = SessionTemplate::with('patient')->where('patient_id',$id)->first();
        return response()->json($session);
    }
    public function store(Request $request) {
    $validated = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'time' => 'required|date_format:H:i',
        'platform_link' => 'required|url',
        'recurrence' => 'required|in:none,weekly',
        'recurrence_days' => 'nullable|array',
        'recurrence_days.*' => 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        'recurrence_end_date' => 'nullable|date|after_or_equal:today',
    ]);

    $doctorId = auth('doctor')->id();

    $data = array_merge($validated, ['doctor_id' => $doctorId]);

    $template = SessionTemplate::create($data);

    return response()->json($template, 201);
}

    public function updateStatus(Request $request, Session $session) {
        $validated = $request->validate([
            'status' => 'required|in:upcoming,completed,missed'
        ]);

        $session->update(['status' => $validated['status']]);
        return response()->json($session);
    }

    public function getPatientSessions(Request $request) {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $sessions = Session::where('patient_id', $validated['patient_id'])
            ->whereBetween('date', [$validated['from'], $validated['to']])
            ->get();

        return response()->json($sessions);
    }

}
