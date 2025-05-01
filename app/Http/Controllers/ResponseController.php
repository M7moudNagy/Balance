<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function index()
    {
        return response()->json(Response::all());
    }

    // Store a new response
    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:questions,id',
            'responses.*.patient_id' => 'required|exists:patients,id',
            'responses.*.answer' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // استخراج question_id من index داخل responses
                    preg_match('/responses\.(\d+)\.answer/', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    if ($index !== null) {
                        $questionId = $request->responses[$index]['question_id'] ?? null;
                        $question = Question::find($questionId);
                        if ($question && !empty($question->options) && !in_array($value, $question->options)) {
                            $fail("The selected answer is invalid for question ID: $questionId. Allowed options: " . implode(', ', $question->options));
                        }
                    }
                }
            ],
            'responses.*.description' => 'nullable|string',
        ]);

        $responses = [];

        foreach ($validated['responses'] as $responseData) {
            // التحقق إن المريض جاوب مرة واحدة فقط لكل سؤال
            $existingResponse = Response::where('question_id', $responseData['question_id'])
                ->where('patient_id', $responseData['patient_id'])
                ->exists();

            if ($existingResponse) {
                return response()->json([
                    'message' => 'You have already answered some of these questions.',
                    'errors' => ['answer' => ['One or more questions have already been answered.']]
                ], 422);
            }

            // إنشاء الاستجابة لكل سؤال
            $responses[] = Response::create([
                'form_id' => $validated['form_id'], // 🔥 إضافة `form_id`
                'question_id' => $responseData['question_id'],
                'patient_id' => $responseData['patient_id'],
                'answer' => $responseData['answer'],
                'description' => $responseData['description'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Responses saved successfully!',
//            'responses' => $responses
        ], 201);
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


