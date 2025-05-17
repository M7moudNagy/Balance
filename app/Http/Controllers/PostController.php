<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\OnePostResource;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'likedByCurrentUser'])->latest()->get();
        return PostResource::collection($posts);
    }


    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $user = auth()->user();

        $data = [
            'content' => $request->content,
            'user_id' => $user->id,
            'user_type' => get_class($user),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create($data);

        return response()->json([
            'message' => 'Challenge created successfully',
        ]);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'likes', 'comments'])->findOrFail($id);
        return new OnePostResource($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if (
            $post->user_id !== auth()->id() ||
            $post->user_type !== get_class(auth()->user())
        ) {
            return response()->json(['message' => 'غير مسموح لك بحذف هذا البوست'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'تم حذف البوست بنجاح']);
    }

}