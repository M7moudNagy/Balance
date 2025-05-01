<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    // Get all questions
    public function index()
    {
        return response()->json(Question::all());
    }

    // Create a new question
    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'type' => 'required|string',
            'question' => 'required|string',
            'options' => 'nullable|json'
        ]);

        $question = Question::create($validated);
        return response()->json($question, 201);
    }

    // Get a single question
    public function show(Question $question)
    {
        return response()->json($question);
    }

    // Update a question
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'type' => 'sometimes|string',
            'question' => 'sometimes|string',
            'options' => 'nullable|json'
        ]);

        $question->update($validated);
        return response()->json($question);
    }

    // Delete a question
    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json(['message' => 'Question deleted successfully']);
    }


}
