<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Events\StartCall;
use App\Events\AcceptCall;
use App\Events\RejectCall;
use App\Events\UserTyping;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\DoctorStatisticController;
use App\Http\Controllers\DoctorPatientController;

use App\Http\Controllers\FormController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\MassageController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::prefix('patient')->group(function () {
    Route::post('/register', [AuthController::class, 'registerPatient']);
    Route::get('/{id}', [PatientController::class, 'show']); 
    Route::put('/{id}', [PatientController::class, 'update']); 
    Route::delete('/{id}', [PatientController::class, 'destroy']);
    Route::post('/login', [AuthController::class, 'loginPatient']);
});
Route::prefix('doctor')->group(function () {
    Route::post('/register', [AuthController::class, 'registerDoctor']);
    Route::post('/login', [AuthController::class, 'loginDoctor']);
});
Route::middleware('auth:patient,doctor')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});
Route::post('/check_email', [ForgetPasswordController::class, 'checkEmail']);

/*
|--------------------------------------------------------------------------
| Dashboards
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:patient', 'user.type:patient'])->get('/patient/dashboard', [PatientController::class, 'dashboard']);
Route::middleware(['auth:doctor', 'user.type:doctor'])->get('/doctor/dashboard', [DoctorController::class, 'dashboard']);

/*
|--------------------------------------------------------------------------
| Resources
|--------------------------------------------------------------------------
*/
Route::resource('/task', TaskController::class);
Route::resource('/form', FormController::class);
Route::resource('/question', QuestionController::class);
Route::resource('/response', ResponseController::class);
Route::resource('/doctor', DoctorController::class);

/*
|--------------------------------------------------------------------------
| Task & Form Status Updates
|--------------------------------------------------------------------------
*/
Route::post('/tasks/{taskId}/patients/{patientId}/status', [TaskController::class, 'updateTaskStatus']);
Route::post('/forms/{formId}/patients/{patientId}/status', [FormController::class, 'updateFormStatus']);

/*
|--------------------------------------------------------------------------
| Form / Responses Access
|--------------------------------------------------------------------------
*/
Route::get('/QuestionsOfForm/{id}', [FormController::class, 'getQuestionsByForm']);
Route::get('/ResponsesOfForm/{id}', [FormController::class, 'getResponsesByForm']);
Route::get('/patient/{patient_id}/form/{form_id}/responses', [FormController::class, 'getPatientResponsesByForm']);
Route::get('/patient/{patient_id}/responses', [FormController::class, 'getPatientResponsesOnAllForms']);

/*
|--------------------------------------------------------------------------
| Tips & Tasks Access
|--------------------------------------------------------------------------
*/
Route::get('/tips/{tipId}/view', [PatientController::class, 'viewTip']);
Route::get('/tasks/{patient_id}', [PatientController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Doctor & Patient Relationship
|--------------------------------------------------------------------------
*/
Route::get('/assign-doctor/{doctor_id}', [DoctorPatientController::class, 'assignDoctorToPatient']);
Route::get('/assigned-patient/details', [DoctorPatientController::class, 'getPatientDetailsForAssignment'])->middleware('auth');
Route::get('/unassigned-doctor/{doctor_id}', [DoctorPatientController::class, 'unassignDoctorFromPatient']);
Route::get('/my_patients', [DoctorController::class, 'my_patients']);
Route::get('/my_patient/{patient_id}', [DoctorController::class, 'getPatientById']);
Route::put('/doctor/patients/{patient_id}/status', [DoctorController::class, 'updatePatientStatus']);
Route::get('/my_patient_tasks/{id}', [DoctorController::class, 'my_patients_tasks']);
Route::get('/my_patient_forms/{id}', [DoctorController::class, 'my_patients_forms']);

/*
|--------------------------------------------------------------------------
| Doctor Statistics
|--------------------------------------------------------------------------
*/
Route::get('/doctors', [DoctorController::class, 'index']); 
Route::prefix('doctors')->group(function () {
    Route::post('{id}/increment-view', [DoctorStatisticController::class, 'incrementDoctorView']);
    Route::post('{id}/rate', [DoctorStatisticController::class, 'rateDoctor']);
    Route::post('{id}/update-patient-count', [DoctorStatisticController::class, 'updateDoctorPatientCount']);
    Route::get('{id}/statistics', [DoctorStatisticController::class, 'getDoctorStatistics']);
});
Route::get('/top-rated-doctors', [DoctorController::class, 'top_rated_doctors']);
Route::get('/dashboard/summary/{doctorId}', [DoctorController::class, 'getDoctorSummary']);

/*
|--------------------------------------------------------------------------
| Messaging & Calling
|--------------------------------------------------------------------------
*/
Route::post('/messages/send', [MassageController::class, 'sendMessage']);

Route::post('/typing', function (Request $request) {
    event(new UserTyping(
        $request->sender_id,
        $request->receiver_id,
        $request->sender_type,
        $request->receiver_type
    ));
    return response()->json(['status' => 'Typing event sent']);
});

Route::post('/start-call', function (Request $request) {
    event(new StartCall($request->caller_id, $request->receiver_id));
    return response()->json(['status' => 'calling']);
});

Route::post('/accept-call', function (Request $request) {
    event(new AcceptCall($request->caller_id, $request->receiver_id));
    return response()->json(['status' => 'call accepted']);
});

Route::post('/reject-call', function (Request $request) {
    event(new RejectCall($request->caller_id, $request->receiver_id));
    return response()->json(['status' => 'call rejected']);
});
