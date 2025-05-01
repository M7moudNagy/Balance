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


    public function consecutiveCommitmentDays($patientId)
{
    // الحصول على كافة المهام المكتملة للمريض مرتبة حسب تاريخ الإتمام
    $completedTasks = PatientTask::where('patient_id', $patientId)
                                ->whereNotNull('completed_at') // تأكد أن المهمة مكتملة
                                ->orderBy('completed_at', 'asc') // ترتيبها حسب تاريخ الإتمام
                                ->get();

    // إذا لم توجد أي مهام مكتملة
    if ($completedTasks->isEmpty()) {
        return response()->json([
            'patient_id' => $patientId,
            'consecutive_commitment_days' => 0
        ]);
    }

    // حساب الأيام المتتالية
    $consecutiveDays = 1; // افترض أن اليوم الأول هو يوم التزام
    $previousTaskDate = \Carbon\Carbon::parse($completedTasks->first()->completed_at);

    foreach ($completedTasks->skip(1) as $task) {
        $currentTaskDate = \Carbon\Carbon::parse($task->completed_at);

        // إذا كان اليوم الحالي هو اليوم التالي مباشرة بعد اليوم السابق
        if ($currentTaskDate->diffInDays($previousTaskDate) == 1) {
            $consecutiveDays++;
        } else {
            // إذا انقطعت الأيام المتتالية
            break;
        }

        $previousTaskDate = $currentTaskDate;
    }

    // إرجاع النتيجة
    return response()->json([
        'patient_id' => $patientId,
        'consecutive_commitment_days' => $consecutiveDays
    ]);
}

public function index($patientId)
{
    $tasks = PatientTask::where('patient_id', $patientId)->get();

    $completedTasks = PatientTask::where('patient_id', $patientId)
                                 ->where('status', 'complete')
                                 ->count();

    $totalTasks = $tasks->count();

    $completionPercentage = 0;

    if ($totalTasks > 0) {
        $completionPercentage = ($completedTasks / $totalTasks) * 100;
    }
    return response()->json([
        'patient_id' => $patientId,
        'completed_tasks' => $completedTasks,
        'completion_percentage' => $completionPercentage
    ]);
}

}
