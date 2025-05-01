<?php

use App\Events\AcceptCall;
use App\Events\RejectCall;
use App\Events\StartCall;
use App\Events\UserTyping;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\DoctorPatientController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MassageController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TipController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('patient')->group(function () {
    Route::post('/register', [AuthController::class, 'registerPatient']);
    Route::post('/login', [AuthController::class, 'loginPatient']);
});
Route::prefix('doctor')->group(function () {
    Route::post('/register', [AuthController::class, 'registerDoctor']);
    Route::post('/login', [AuthController::class, 'loginDoctor']);
});
Route::middleware('auth:patient,doctor')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:patient,doctor')->post('/refresh', [AuthController::class, 'refresh']);
/******************************************************************/
Route::middleware(['auth:patient', 'user.type:patient'])->group(function () {
    Route::get('/patient/dashboard', [PatientController::class, 'dashboard']);
});
Route::middleware(['auth:doctor', 'user.type:doctor'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard']);
});
/******************************************************************/
Route::post('/check_email', [ForgetPasswordController::class, 'checkEmail']);
/******************************************************************/
Route::resource('/category', CategoryController::class);
Route::resource('/task', TaskController::class);
Route::post('/tasks/{taskId}/patients/{patientId}/status', [TaskController::class, 'updateTaskStatus']);
Route::resource('/tip', TipController::class);
Route::get('/tips/{tipId}/view', [PatientController::class, 'viewTip']);
Route::resource('/form', FormController::class);
Route::post('/forms/{formId}/patients/{patientId}/status', [FormController::class, 'updateFormStatus']);
Route::resource('/question', QuestionController::class);
Route::resource('/response', ResponseController::class);
/******************************************************************/
Route::resource('/doctor', DoctorController::class);
Route::get('/my_patient/{id}', [DoctorController::class, 'my_patients']);
Route::post('/assign-doctor', [DoctorPatientController::class, 'assignDoctorToPatient']);
Route::get('/my_patient_tasks/{id}', [DoctorController::class, 'my_patients_tasks']);
Route::get('/my_patient_tips/{id}', [DoctorController::class, 'my_patients_tips']);
Route::get('/my_patient_forms/{id}', [DoctorController::class, 'my_patients_forms']);
Route::get('/QuestionsOfForm/{id}', [FormController::class, 'getQuestionsByForm']);
Route::get('/ResponsesOfForm/{id}', [FormController::class, 'getResponsesByForm']);
Route::get('/patient/{patient_id}/form/{form_id}/responses', [FormController::class, 'getPatientResponsesByForm']);
Route::get('/patient/{patient_id}/responses', [FormController::class, 'getPatientResponsesOnAllForms']);
Route::get('/top-rated-doctors', [DoctorController::class, 'top_rated_doctors']);
Route::get('/dashboard/summary/{doctorId}', [DoctorController::class, 'getDoctorSummary']);
/******************************************************************/
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
/******************************************************************/





