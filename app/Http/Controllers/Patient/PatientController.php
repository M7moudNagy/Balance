<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Tip;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function viewTip($tipId)
    {
        $patientId = request()->get('patient_id', 1); // بدل 1 بـ أي ID عايزه، أو بعت الـ ID في البوستمان

        // لما تفعل auth:
        // $patientId = auth()->id();

        $pivotData = DB::table('patient_tip')
            ->where('patient_id', $patientId)
            ->where('tip_id', $tipId)
            ->first();

        if (! $pivotData) {
            return response()->json(['message' => 'Not found or unauthorized'], 404);
        }

        if ($pivotData->status === 'unread') {
            DB::table('patient_tip')
                ->where('patient_id', $patientId)
                ->where('tip_id', $tipId)
                ->update(['status' => 'read']);
        }

        $tip = Tip::find($tipId);

        return response()->json([
            'tip' => $tip,
            'status' => 'read'
        ]);
    }


}
