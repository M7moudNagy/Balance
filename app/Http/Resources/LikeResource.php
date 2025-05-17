<?php
namespace App\Http\Resources;

use App\Traits\HasUserImage;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    use HasUserImage;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->fullname,
                'type' => class_basename($this->user),
                'User_image' => $this->getUserImage($this->user),

            ],
        ];
    }
}

