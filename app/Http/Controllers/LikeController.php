<?php

namespace App\Http\Controllers;

use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle($postId)
    {
        $user = Auth::guard('doctor')->user() ?? Auth::guard('patient')->user();

        $like = PostLike::where('post_id', $postId)
                        ->where('user_id', $user->id)
                        ->where('user_type', get_class($user))
                        ->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false]);
        } else {
            PostLike::create([
                'post_id' => $postId,
                'user_id' => $user->id,
                'user_type' => get_class($user)
            ]);
            return response()->json(['liked' => true]);
        }
    }
}