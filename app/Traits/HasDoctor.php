<?php

namespace App\Traits;

use App\Models\DoctorPatient;

trait HasDoctor
{
    public function hasDoctor($id)
    {
        if (DoctorPatient::where('patient_id', $id)->exists()){
           return "true";
        }else{
            return "false";
        }

        return null;
    }
}
