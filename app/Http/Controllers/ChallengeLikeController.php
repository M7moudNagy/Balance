<?php

namespace App\Http\Controllers;

use App\Models\ChallengeLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeLikeController extends Controller
{
    public function toggle($challengeId)
    {
        $user = Auth::guard('doctor')->user() ?? Auth::guard('patient')->user();

        $like = ChallengeLike::where('challenge_id', $challengeId)
                        ->where('user_id', $user->id)
                        ->where('user_type', get_class($user))
                        ->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false]);
        } else {
            ChallengeLike::create([
                'challenge_id' => $challengeId,
                'user_id' => $user->id,
                'user_type' => get_class($user)
            ]);
            return response()->json(['liked' => true]);
        }
    }
}
