<?php

namespace App\Traits;

trait StatusOfChallengeOrPost
{
    public function StatusOfChallengeOrPost($pc)
    {
        if ($pc instanceof \App\Models\Post) {
            return 'post';
        }

        if ($pc instanceof \App\Models\Challenge) {
            return 'challange';
        }

        return null;
    }
}