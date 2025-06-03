<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class DoctorPatientSeeder extends Seeder
{
    public function run()
    {
        $doctor = Doctor::first();     // أو use factory later
        $patient = Patient::first();   // أو use factory later

        if (!$doctor || !$patient) {
            $this->command->warn("لا يوجد دكتور أو مريض في قاعدة البيانات.");
            return;
        }

        // تحقق من عدم الربط المسبق
        $alreadyAssigned = DB::table('doctor_patients')
            ->where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->exists();

        if (!$alreadyAssigned) {
            DB::table('doctor_patients')->insert([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'fullname' => 'محمد عبد الله',
                'email' => 'mohamed@example.com',
                'phoneNumber' => '0123456789',
                'age' => 28,
                'typeOfAddiction' => 'تدخين',
                'durationOfAddication' => '2 سنين',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->command->info('تم ربط المريض بالدكتور بنجاح.');
        } else {
            $this->command->warn('المريض مرتبط بالفعل بدكتور.');
        }
    }
}
