<?php

use App\Events\StartCall;
use App\Events\AcceptCall;

use App\Events\RejectCall;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MassageController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;

use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\ChallengeLikeController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\DoctorPatientController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\DoctorStatisticController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\ChallengeCommentController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::prefix('patient')->group(function () {
    Route::post('/register', [AuthController::class, 'registerPatient']);
    Route::post('/login', [AuthController::class, 'loginPatient']);
});

Route::prefix('doctor')->group(function () {
    Route::post('/register', [AuthController::class, 'registerDoctor']);
    Route::post('/login', [AuthController::class, 'loginDoctor']);
});

Route::post('/check_email', [ForgetPasswordController::class, 'checkEmail']);

/*
|--------------------------------------------------------------------------
| Routes for Both Authenticated (Patient or Doctor)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:patient,doctor')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});
/*
|--------------------------------------------------------------------------
| Routes Requiring Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('auth:patient,doctor')->prefix('patient')->group(function () {
    Route::get('/task-me', [PatientController::class, 'task_me']);
    Route::get('/{id}', [PatientController::class, 'show']); 
    Route::get('/dashboard', [PatientController::class, 'dashboard']);
});
Route::middleware('auth:doctor')->prefix('patient')->group(function () {
    Route::put('/{id}', [PatientController::class, 'update']);
    Route::delete('/{id}', [PatientController::class, 'destroy']);
});

Route::middleware('auth:doctor,patient')->group(function () {
    Route::get('/dashboard', [DoctorController::class, 'dashboard']);
    Route::get('/my_patients', [DoctorController::class, 'my_patients']);
    Route::get('/my_patient/{patient_id}', [DoctorController::class, 'getPatientById']);
    Route::put('/patients/{patient_id}/status', [DoctorController::class, 'updatePatientStatus']);
    Route::get('/my_patient_tasks/{patient_id}', [DoctorController::class, 'my_patients_tasks']);
    Route::post('/assign-doctor/{doctor_id}', [DoctorPatientController::class, 'assignDoctorToPatient']);
    Route::get('/assigned-patient/details', [DoctorPatientController::class, 'getPatientDetailsForAssignment']);
    Route::get('/unassigned-doctor/{doctor_id}', [DoctorPatientController::class, 'unassignDoctorFromPatient']);
    Route::get('/dashboard/summary/{doctorId}', [DoctorController::class, 'getDoctorSummary']);

    Route::prefix('doctors')->group(function () {
        Route::post('{id}/increment-view', [DoctorStatisticController::class, 'incrementDoctorView']);
        Route::post('{id}/rate', [DoctorStatisticController::class, 'rateDoctor']);
        Route::post('{id}/update-patient-count', [DoctorStatisticController::class, 'updateDoctorPatientCount']);
        Route::get('{id}/statistics', [DoctorStatisticController::class, 'getDoctorStatistics']);
    });
});

/*
|--------------------------------------------------------------------------
| Resources Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:patient,doctor')->group(function () {
    Route::get('/my_patients_tasks/{patient_id}', [DoctorController::class, 'my_patients_tasks']);
    Route::resource('/task', TaskController::class);
    Route::resource('/question', QuestionController::class);
    Route::resource('/response', ResponseController::class);
});

/*
|--------------------------------------------------------------------------
| Task Status Update
|--------------------------------------------------------------------------
*/
Route::middleware('auth:doctor')->get('/tasks/{task_id}', [TaskController::class, 'markTaskInProgress']);
/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:doctor,patient')->group(function () {
    Route::get('/top-rated-doctors', [DoctorController::class, 'top_rated_doctors']);
    Route::resource('/doctor', DoctorController::class);
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/like', [LikeController::class, 'toggle']);
    Route::post('/posts/{id}/comment', [CommentController::class, 'store']);
    Route::get('/challenges', [ChallengeController::class, 'index']);
    Route::get('/challenges/{id}', [ChallengeController::class, 'show']);
    Route::post('/challenges', [ChallengeController::class, 'store']);
    Route::delete('/challenges/{id}', [ChallengeController::class, 'destroy']);
    Route::post('/challenges/{id}/like', [ChallengeLikeController::class, 'toggle']);
    Route::post('/challenges/{id}/comment', [ChallengeCommentController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Messaging & Calling (ممكن تحتاج auth حسب التطبيق)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:patient,doctor')->group(function () {
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
});