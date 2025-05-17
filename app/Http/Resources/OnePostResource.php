<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Traits\HasUserImage;

use Illuminate\Http\Resources\Json\JsonResource;

class OnePostResource extends JsonResource
{
    use HasUserImage;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'image' => $this->image,
            'User_image' => $this->getUserImage($this->user),
            'created_at' => $this->created_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->fullname,
                'User_image' => $this->getUserImage($this->user),
                'type' => class_basename($this->user),
            ],
            'likes_count' => $this->likes->count(),
            'comments_count' => $this->comments->count(),
            'comments' => CommentResource::collection($this->comments),
            'likes' => LikeResource::collection($this->likes),
        ];
    }
}
