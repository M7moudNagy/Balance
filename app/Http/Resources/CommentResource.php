<?php
namespace App\Http\Resources;

use App\Traits\HasUserImage;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    use HasUserImage;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->comment,
            'created_at' => $this->created_at,

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->fullname,
                'User_image' => $this->getUserImage($this->user),
                'type' => class_basename($this->user),
            ],
        ];
    }
}
