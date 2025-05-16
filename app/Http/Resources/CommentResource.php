<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->comment,
            'created_at' => $this->created_at,

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->fullname,
                'type' => class_basename($this->user),
            ],
        ];
    }
}
