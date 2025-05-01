<?php

namespace App\Http\Controllers;

use App\http\Helpers\ResponseHelper;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\TaskResources;
use App\Http\Resources\TipResource;
use App\Models\Patient;
use App\Models\PatientTask;
use App\Models\Task;
use App\Models\Tip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'title' => 'required|string',
        'description' => 'nullable|string',
        'assign_date' => 'required|date',
        'target_date' => 'required|date',
        'repeat' => 'nullable|string',
        'days' => 'nullable|array',
        'notes' => 'nullable|string',
        'patients' => 'required|array', // استقبال المرضى كـ Array
        'patients.*' => 'exists:patients,id',// كل مريض لازم يكون موجود
        'category_id' => 'required|exists:categories,id', // 🔹 التحقق من وجود الفئة في قاعدة البيانات

        ]);
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assign_date' => $request->assign_date,
            'target_date' => $request->target_date,
            'repeat' => $request->repeat,
            'days' => json_encode($request->days), // تحويل Array إلى JSON
            'notes' => $request->notes,
            'doctor_id' => $request->doctor_id, // تعيين الدكتور الحالي
            'category_id' => $request->category_id, // 🔹 ربط المهمة بالفئة

        ]);

        // تحضير البيانات مع status
        $patientsWithStatus = [];
        foreach ($request->patients as $patientId) {
            $patientsWithStatus[$patientId] = ['status' => 'pending'];
        }
        $task->patients()->attach($patientsWithStatus);
//        foreach ($request->patients as $patientId) {
//            \App\Models\PatientTask::create([
//                'task_id' => $task->id,
//                'patient_id' => $patientId,
//                'status' => 'pending',
//            ]);
//        }
        return response()->json(['message' => 'Task created successfully', 'task' => $task]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::with([
            'category',
            'patients' => function($q) {
                $q->withPivot('status');
            }
        ])->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return new TaskResources($task);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'assign_date' => 'required|date',
            'target_date' => 'required|date',
            'repeat' => 'required|numeric',
            'days' => 'required|array',
            'notes' => 'nullable|min:50',
            'patients' => 'nullable|array', // لو عايز تحدّث المرضى كمان
            'patients.*.id' => 'required|exists:patients,id',
            'patients.*.status' => 'required|in:pending,in_progress,completed,over_due',
        ]);

        // تحديث بيانات المهمة نفسها
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'assign_date' => $request->assign_date,
            'target_date' => $request->target_date,
            'repeat' => $request->repeat,
            'days' => json_encode($request->days),
            'notes' => $request->notes,
        ]);

        // لو تم إرسال بيانات المرضى للتعديل
        if ($request->has('patients')) {
            $patientsData = [];
            foreach ($request->patients as $patient) {
                $patientsData[$patient['id']] = ['status' => $patient['status']];
            }
            // مزامنة المرضى الحاليين بالمهمة وتحديث حالاتهم
            $task->patients()->sync($patientsData);
        }
        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task,$id)
    {
        $task = Task::where('id',$id);
        $task->delete();
        return response()->json(['task deleted successfully']);
    }

    public function updateTaskStatus(Request $request, $taskId, $patientId)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,over_due'
        ]);

        $task = Task::findOrFail($taskId);

        // تأكد إن المريض مرتبط بالمهمة
        if (!$task->patients()->where('patient_id', $patientId)->exists()) {
            return response()->json(['message' => 'Patient not assigned to this task'], 404);
        }

        $currentStatus = $task->patients()->where('patient_id', $patientId)->first()->pivot->status;
        $newStatus = $request->status;

        // منطق الحماية:
        if ($newStatus === 'in_progress' && $currentStatus !== 'pending') {
            return response()->json(['message' => 'Task can only move to in_progress from pending'], 400);
        }

        if ($newStatus === 'completed' && $currentStatus === 'completed') {
            return response()->json(['message' => 'Task already completed'], 400);
        }

        // تحديث الحالة
        $task->patients()->updateExistingPivot($patientId, ['status' => $newStatus]);
        return response()->json(['message' => "Task marked as {$newStatus} for patient"]);
    }

}
