<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Resources\OnePostResource;

class ChallengeController extends Controller
{
    public function index()
    {
        $challenges = Challenge::with(['user', 'likedByCurrentUser'])->latest()->get();
        return PostResource::collection($challenges);
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
            'type'=> 'Challenge'
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('challenges', 'public');
        }

        $challenge = Challenge::create($data);

        return response()->json([
            'message' => 'Challenge created successfully',
        ]);
    }

    public function show($id)
    {
        $challenge = Challenge::with(['user', 'likes', 'comments'])->findOrFail($id);
        return new OnePostResource($challenge);
    }

    public function destroy($id)
    {
        $challenge = Challenge::findOrFail($id);

        if (
            $challenge->user_id !== auth()->id() ||
            $challenge->user_type !== get_class(auth()->user())
        ) {
            return response()->json(['message' => 'غير مسموح لك بحذف هذا التحدي'], 403);
        }

        $challenge->delete();

        return response()->json(['message' => 'تم حذف التحدي بنجاح']);
    }}
