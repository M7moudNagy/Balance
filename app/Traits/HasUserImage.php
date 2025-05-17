<?php

namespace App\Traits;

trait HasUserImage
{
    public function getUserImage($user)
    {
        if ($user instanceof \App\Models\Doctor) {
            return $user->image ? asset('storage/' . $user->image) : null;
        }

        if ($user instanceof \App\Models\Patient) {
            return $user->avatar ? asset('storage/avatars/' . $user->avatar) : null;
        }

        return null;
    }
}
