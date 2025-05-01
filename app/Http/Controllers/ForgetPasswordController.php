<?php
namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Mail\TestMail;


class ForgetPasswordController extends Controller
{
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $doctor = Doctor::where('email', $request->email)->first();
        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found.'], 404);
        }

        $token = Password::broker('doctors')->createToken($doctor);
          Mail::to($request->email)->send(new TestMail($token));
            return response()->json(['message' => 'Password reset link sent successfully.']);

    }
//    {
//        $request->validate([
//            'email' => 'required|email|exists:doctors,email',
//        ]);
//
//        Mail::to($request->email)->send(new TestMail());
//        return response()->json(['massage' => 'Email has been sent']);
//    }
}

