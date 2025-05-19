<?php
namespace App\Http\Resources;

use App\Traits\HasUserImage;
use App\Traits\StatusOfChallengeOrPost;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    use HasUserImage;
    use StatusOfChallengeOrPost;

    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'status' => $this->StatusOfChallengeOrPost($this->resource),
            'content' => $this->content,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'user' => [
                'id' => $this->user->id,
                'User_image' => $this->getUserImage($this->user),
                'name' => $this->user->fullname,
                'type' => class_basename($this->user),
            ],
            'likes_count' => $this->likes->count(),
            'comments_count' => $this->comments->count(),
            'is_liked_by_me' => $this->likedByCurrentUser ? true : false,
            'comments' => CommentResource::collection($this->comments),
            'likes' => LikeResource::collection($this->likes),
        ];
    }
}

