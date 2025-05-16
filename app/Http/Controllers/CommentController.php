<?php

namespace App\Http\Controllers;

use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $user = Auth::guard('doctor')->user() ?? Auth::guard('patient')->user();

        $comment = PostComment::create([
            'post_id' => $postId,
            'comment' => $request->comment,
            'user_id' => $user->id,
            'user_type' => get_class($user)
        ]);

        return response()->json($comment);
    }
}
