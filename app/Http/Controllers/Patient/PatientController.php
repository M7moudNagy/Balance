<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientTask;
use App\Models\Task;
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
    // الحصول على جميع المهام المكتملة
    $completedTasks = PatientTask::where('patient_id', $patientId)
                                 ->where('status', 'completed') // باستخدام status بدلاً من completed_at
                                 ->orderBy('updated_at', 'asc') // ترتيب المهام حسب آخر تعديل
                                 ->get();

    // إذا ما فيش مهام مكتملة، إرجاع 0
    if ($completedTasks->isEmpty()) {
        return 0;
    }

    $consecutiveDays = 1; // البداية من يوم واحد كأول يوم ملتزم
    $previousTaskDate = null;

    foreach ($completedTasks as $task) {
        $taskDate = $task->updated_at->toDateString(); // تاريخ المهمة المُكتملة (يفضل استخدام التاريخ المحدَّث)

        // إذا كان التاريخ الحالي للمهمة يختلف عن التاريخ السابق، نعتبرها يوم جديد
        if ($previousTaskDate && $previousTaskDate != $taskDate) {
            // إذا الفرق يوم واحد
            if (now()->subDay()->toDateString() == $taskDate) {
                $consecutiveDays++;
            } else {
                break; // لو مش يوم متتالي، خلصنا الحساب
            }
        }

        $previousTaskDate = $taskDate;
    }

    return $consecutiveDays;
}


public function index($patientId)
{
    $tasks = PatientTask::where('patient_id', $patientId)->get();

    $totalTasks = $tasks->count();
    $completedTasks = $tasks->where('status', 'Completed')->count();

    $completionPercentage = 0;
    if ($totalTasks > 0) {
        $completionPercentage = ($completedTasks / $totalTasks) * 100;
    }

    $consecutiveDays = $this->consecutiveCommitmentDays($patientId);

    // المهام المعلقة
    $pendingTasks = PatientTask::with('task')
        ->where('patient_id', $patientId)
        ->where('status', 'Pending')
        ->get()
        ->map(function ($pt) {
            return [
                'task_id' => $pt->task_id,
                'title' => $pt->task->title ?? null,
                'description' => $pt->task->description ?? null,
                'status' => $pt->status,
            ];
        });

    // المهام قيد التنفيذ
    $inprogressTasks = PatientTask::with('task')
        ->where('patient_id', $patientId)
        ->where('status', 'In Progress')
        ->get()
        ->map(function ($pt) {
            return [
                'task_id' => $pt->task_id,
                'title' => $pt->task->title ?? null,
                'description' => $pt->task->description ?? null,
                'status' => $pt->status,
            ];
        });

    // المهام المنتهية
    $completedTasksData = PatientTask::with('task')
        ->where('patient_id', $patientId)
        ->where('status', 'Completed')
        ->get()
        ->map(function ($pt) {
            return [
                'task_id' => $pt->task_id,
                'title' => $pt->task->title ?? null,
                'description' => $pt->task->description ?? null,
                'status' => $pt->status,
                'completed_at' => $pt->completed_at,
            ];
        });

    // المهام المتأخرة
    $overdueTasks = PatientTask::with('task')
        ->where('patient_id', $patientId)
        ->where('status', 'Overdue')
        ->get()
        ->map(function ($pt) {
            return [
                'task_id' => $pt->task_id,
                'title' => $pt->task->title ?? null,
                'description' => $pt->task->description ?? null,
                'status' => $pt->status,
            ];
        });

    return response()->json([
        'patient_id' => $patientId,
        'completion_percentage' => $completionPercentage,
        'consecutive_commitment_days' => $consecutiveDays,
        'pendingTasks' => $pendingTasks,
        'inprogressTasks' => $inprogressTasks,
        'completedTasks' => $completedTasksData,
        'overdueTasks' => $overdueTasks,
    ]);
}
}   