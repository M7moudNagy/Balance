<?php

namespace App\Http\Controllers;

use App\Http\Resources\FormResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\TipResource;
use App\Models\Form;
use App\Models\Patient;
use App\Models\PatientForm;
use App\Models\Question;
use App\Models\Response;
use App\Models\Task;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::all();
        return response()->json($forms);
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

        try {
            // ✅ 1. التحقق من صحة البيانات
            $validated = $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string',
                'assign_date' => 'required|date',
                'target_date' => 'required|date',
                'repeat' => 'nullable|string',
                'notes' => 'nullable|string',
                'doctor_id' => 'required|exists:doctors,id',
                'patients' => 'required|array',
                'patients.*' => 'exists:patients,id',
                'questions' => 'required|array',
                'questions.*.type' => 'required|string',
                'questions.*.question' => 'required|string',
                'questions.*.options' => 'nullable|array',
            ]);

            // ✅ 2. إنشاء الفورم
            $form = Form::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'assign_date' => $validated['assign_date'],
                'target_date' => $validated['target_date'],
                'repeat' => $validated['repeat'] ?? '',
                'notes' => $validated['notes'] ?? '',
                'doctor_id' => $validated['doctor_id'],
            ]);

            // ✅ 3. ربط المرضى بالفورم
            if (!empty($validated['patients'])) {
                $form->patients()->attach($validated['patients']);
            }

            // ✅ 4. إضافة الأسئلة المرتبطة بالفورم
            if (!empty($validated['questions'])) {
                foreach ($validated['questions'] as $question) {
                    $form->questions()->create([
                        'type' => $question['type'],
                        'question' => $question['question'],
                        'options' => $question['options'] ?? [],
                    ]);
                }
            }

            // ✅ 5. إرسال الاستجابة
            return response()->json([
                'message' => 'Form created successfully!'
//                'form' => $form->load(['questions', 'patients'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $patients = PatientForm::where('form_id',$id)->get();
        $formpatients = Patient::whereIn('id', $patients->pluck('patient_id'))->get();
        $form = Form::find($id)->first();

        if (!$form) {
            return response()->json(['message' => 'Form not found'], 404);
        }
        return response()->json(['form' => new FormResource($form), 'patients' => PatientResource::collection($formpatients)], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $form = Form::find('id',$id);

        request()->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
            'assign_date' => 'required',
            'target_date' => 'required',
            'repeat' => 'required|numeric',
            'days' => 'required',
            'notes' => 'min:50',
        ]);
        $form->update($request->all());
        return response()->json($form);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $form = Form::where('id',$id);
        $form->delete();
        return response()->json(['form deleted successfully']);
    }
    public function getQuestionsByForm($form_id)
    {
        // جلب جميع الأسئلة المرتبطة بـ form معين
        $questions = Question::where('form_id', $form_id)->get();

        // التأكد من وجود بيانات
        if ($questions->isEmpty()) {
            return response()->json(['message' => 'No questions found for this form.'], 404);
        }

        return response()->json([
            'form_id' => $form_id, // إظهار form_id مرة واحدة
            'questions' => $questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'type' => $question->type,
                    'question' => $question->question,
                    'options' => $question->options,
                    'created_at' => $question->created_at,
                    'updated_at' => $question->updated_at,
                ];
            }),
        ]);
    }

    public function getResponsesByForm($form_id)
    {
        // جلب جميع الاستجابات مع العلاقات المرتبطة
        $responses = Response::where('form_id', $form_id)
            ->with(['question', 'patient']) // تأكد من جلب المرضى المرتبطين
            ->get();

        // التأكد من وجود بيانات
        if ($responses->isEmpty()) {
            return response()->json(['message' => 'No responses found for this form.'], 404);
        }

        // استخراج بيانات جميع المرضى
        $patients = $responses->pluck('patient')->unique('id')->values();

        return response()->json([
            'form_id' => $form_id,
            'patients' => $patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'mobile_number' => $patient->mobile_number,
                    'date_of_birth' => $patient->date_of_birth,
                    'gender' => $patient->gender,
                    'governorate' => $patient->governorate,
                    'address' => $patient->address,
                    'email' => $patient->email,
                ];
            }),
            'responses' => $responses->map(function ($response) {
                return [
                    'id' => $response->id,
                    'question_id' => $response->question_id,
                    'description' => $response->description,
                    'created_at' => $response->created_at,
                    'updated_at' => $response->updated_at,
                    'question' => [
                        'id' => $response->question->id,
                        'type' => $response->question->type,
                        'question' => $response->question->question,
                        'options' => $response->question->options,
                    ],
                    'answer' => $response->answer,
                    'patient_id' => $response->patient_id, // 🔥 أضف الـ `patient_id` هنا حتى تقدر تربط كل إجابة بالمريض المناسب
                ];
            }),
        ]);
    }

    public function getPatientResponsesByForm($patient_id, $form_id)
    {
        $responses = Response::where('form_id', $form_id)
            ->where('patient_id', $patient_id)
            ->with(['question'])
            ->get();

        if ($responses->isEmpty()) {
            return response()->json(['message' => 'No responses found for this patient on this form.'], 404);
        }

        return response()->json([
            'form_id' => $form_id,
            'patient_id' => $patient_id,
            'responses' => $responses->map(function ($response) {
                return [
                    'id' => $response->id,
                    'question_id' => $response->question_id,
                    'description' => $response->description,
                    'created_at' => $response->created_at,
                    'updated_at' => $response->updated_at,
                    'question' => [
                        'id' => $response->question->id,
                        'type' => $response->question->type,
                        'question' => $response->question->question,
                        'options' => $response->question->options,
                    ],
                    'answer' => $response->answer,
                ];
            }),
        ]);
    }

    public function getPatientResponsesOnAllForms($patient_id)
    {
        $responses = Response::where('patient_id', $patient_id)
            ->with(['form', 'question'])
            ->get();

        if ($responses->isEmpty()) {
            return response()->json(['message' => 'No responses found for this patient.'], 404);
        }

        return response()->json([
            'patient_id' => $patient_id,
            'responses' => $responses->groupBy('form_id')->map(function ($formResponses, $form_id) {
                return [
                    'form_id' => $form_id,
                    'responses' => $formResponses->map(function ($response) {
                        return [
                            'id' => $response->id,
                            'question_id' => $response->question_id,
                            'description' => $response->description,
                            'created_at' => $response->created_at,
                            'updated_at' => $response->updated_at,
                            'question' => [
                                'id' => $response->question->id,
                                'type' => $response->question->type,
                                'question' => $response->question->question,
                                'options' => $response->question->options,
                            ],
                            'answer' => $response->answer,
                        ];
                    }),
                ];
            })->values(),
        ]);
    }

    public function updateFormStatus(Request $request, $formId, $patientId)
    {
        $request->validate([
            'status' => 'required|in:NotStarted,In Progress,Submitted,overdue',
        ]);

        $form = Form::findOrFail($formId);

        // تأكد إن المريض مرتبط بالـ form
        if (!$form->patients()->where('patient_id', $patientId)->exists()) {
            return response()->json(['message' => 'Patient not assigned to this form'], 404);
        }

        $currentStatus = $form->patients()->where('patient_id', $patientId)->first()->pivot->status;
        $newStatus = $request->status;

        // منطق الحماية
        if ($newStatus === 'In Progress' && $currentStatus !== 'NotStarted') {
            return response()->json(['message' => 'Form can only move to In Progress from NotStarted'], 400);
        }

        if ($newStatus === 'Submitted' && $currentStatus === 'Submitted') {
            return response()->json(['message' => 'Form already submitted'], 400);
        }

        // تحديث الحالة
        $form->patients()->updateExistingPivot($patientId, ['status' => $newStatus]);

        return response()->json(['message' => "Form marked as {$newStatus} for patient"]);
    }



}
