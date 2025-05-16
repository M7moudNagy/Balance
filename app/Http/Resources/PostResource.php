<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->fullname,
                'type' => class_basename($this->user),
            ],
            'likes_count' => $this->likes->count(),
            'comments_count' => $this->comments->count(),
            // 'comments' => CommentResource::collection($this->comments),
            // 'likes' => LikeResource::collection($this->likes),
        ];
    }
}

