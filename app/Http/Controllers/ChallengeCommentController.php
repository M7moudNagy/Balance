<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChallengeComment;
use Illuminate\Support\Facades\Auth;

class ChallengeCommentController extends Controller
{
    public function store(Request $request, $challengeId)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $user = Auth::guard('doctor')->user() ?? Auth::guard('patient')->user();

        $comment = ChallengeComment::create([
            'challenge_id' => $challengeId,
            'comment' => $request->comment,
            'user_id' => $user->id,
            'user_type' => get_class($user)
        ]);

        return response()->json($comment);
    }
}

