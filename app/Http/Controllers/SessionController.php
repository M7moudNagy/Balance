<?php
namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Models\Session;
use Illuminate\Http\Request;
use App\Models\DoctorPatient;
use App\Models\SessionTemplate;
use App\Traits\HasSessionCalculations;

class SessionController extends Controller
{
    use HasSessionCalculations;
    
    public function index()
    {
        $sessions = Session::all();
        return response()->json($sessions);
    }

    public function show(Session $session)
    {
        return response()->json($session);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'platform_link' => 'required|url',
        ]);

        $session = Session::create($validated);
        return response()->json($session, 201);
    }

    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'doctor_id' => 'sometimes|exists:doctors,id',
            'patient_id' => 'sometimes|exists:patients,id',
            'date' => 'sometimes|date',
            'time' => 'sometimes|date_format:H:i',
            'platform_link' => 'sometimes|url',
            'status' => 'sometimes|in:upcoming,completed,missed'
        ]);

        $session->update($validated);
        return response()->json($session);
    }

    public function destroy(Session $session)
    {
        $session->delete();
        return response()->json(null, 204);
    }

    public function getDoctorSessions($doctorId)
    {
        $sessions = Session::where('doctor_id', $doctorId)->get();
        return response()->json($sessions);
    }

    public function getPatientSessions(Request $request)
    {
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
    public function getAuthPatientDailySessions()
    {
        $patient = auth('patient')->user();
        $patient_id=$patient->id;
        $sessions = Session::where('patient_id', $patient_id)->get();
        return response()->json(['myDailySessions'=> $sessions]);
    }
    public function getPatientDailySessions($id)
    {
        $doctor = auth('doctor')->user();
        $doctor_id=$doctor->id;
        $sessions = Session::where('patient_id', $id)->where('doctor_id', $doctor_id)->get();
        return response()->json(['myPatientdailySessions'=> $sessions]);
    }
    public function getDoctorDailySessions()
    {
        $doctor = auth('doctor')->user();
        $doctor_id=$doctor->id;
        $sessions = Session::where('doctor_id', $doctor_id)->get();
        return response()->json(['daily-sessions'=> $sessions]);
    }

    public function getPatientDoctorSessionStats()
        {
            $patient = auth('patient')->user();

            if (!$patient) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $sessionTemplate = SessionTemplate::with('doctor')->where('patient_id', $patient->id)->first();
            // dd($sessionTemplate);
            if (!$sessionTemplate->recurrence_end_date) {
                return response()->json(['error' => 'No recurrence_end_date found'], 404);
            }

            $sessions = Session::with('doctor')
                ->where('patient_id', $patient->id)
                ->where('doctor_id', $sessionTemplate->doctor_id)
                ->whereDate('date', '<=', $sessionTemplate->recurrence_end_date)
                ->get();

            $total = $this->hasSessionCalculations($sessionTemplate);
            $completed = $sessions->where('status', 'completed')->count();
            $missed = $sessions->where('status', 'missed')->count();
            $remaining = $total - ($completed + $missed);
                if ($remaining < 0) {
                    $remaining = 0;
                }

            return response()->json([
                'doctor' => new DoctorResource($sessionTemplate->doctor),
                'total_sessions' => $total,
                'completed_sessions' => $completed,
                'missed_sessions' => $missed,
                'remaining_sessions' => $remaining,
                'session_details' => $sessionTemplate->makeHidden('doctor'),
            ]);
        }
}
