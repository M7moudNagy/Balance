<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Patient;
use App\Models\PatientTask;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function index()
    {
        return response()->json(Response::all());
    }

    public function store(Request $request)
    {
        
        foreach ($request->answers as $ans) {
            $question = Question::where('id', $ans['question_id'])
                ->where('task_id', $request->task_id)
                ->first();

            if (!$question) {
                return response()->json([
                    'error' => "Invalid question-task mapping"
                ], 422);
            }

            Response::create([
                'patient_id' => $request->patient_id,
                'task_id' => $request->task_id,
                'question_id' => $ans['question_id'],
                'answer_text' => $ans['answer_text'],
                'time_taken' => $ans['time_taken'] ?? null,
            ]);
        }
        $task = Task::find($request->task_id);

        if ($task->task_points > 0) {
            $patient = Patient::find($request->patient_id);

            if ($patient) {
                $patient->points += $task->task_points;
                $patient->save();
            }
        }
        $updateStatus = PatientTask::where('patient_id',$request->patient_id)->first();
        $updateStatus->status = "Completed";
        $updateStatus->completed_at =now();
        $updateStatus->save();

        return response()->json(['message' => 'Answers saved successfully']);
    }
    
    public function show(Response $response)
    {
        return response()->json($response);
    }

    public function update(Request $request, Response $response)
    {
        $validated = $request->validate([
            'answer' => 'sometimes|string',
            'description' => 'nullable|string',
        ]);

        $response->update($validated);
        return response()->json($response);
    }

    public function destroy(Response $response)
    {
        $response->delete();
        return response()->json(['message' => 'Response deleted successfully']);
    }

}


