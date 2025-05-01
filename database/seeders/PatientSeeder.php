<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::insert([[
            "first_name"=> "Ahmed",
            "last_name"=> "Ali",
            "email"=> "ahmed33.ali@example.com",
            "password"=> bcrypt("123456789"),
            "mobile_number"=> "01123456789",
            "governorate"=> "القاهره",
            "gender"=> "male",
            "date_of_birth"=> "1985-06-15",
            "address"=> "123 شارع النصر, القاهرة",
            "doctor_id"=> 1,
        ],
            [
                "first_name"=> "Ahmed",
                "last_name"=> "Ali",
                "email"=> "ahmed0033.ali@example.com",
                "password"=> bcrypt("123456789"),
                "mobile_number"=> "01120456789",
                "governorate"=> "القاهره",
                "gender"=> "male",
                "date_of_birth"=> "1985-06-15",
                "address"=> "123 شارع النصر, القاهرة",
                "doctor_id"=> 1,
            ],
            [
                "first_name"=> "Ahmed",
                "last_name"=> "Ali",
                "email"=> "ahmed3398.ali@example.com",
                "password"=> bcrypt("123456789"),
                "mobile_number"=> "01124856789",
                "governorate"=> "القاهره",
                "gender"=> "male",
                "date_of_birth"=> "1985-06-15",
                "address"=> "123 شارع النصر, القاهرة",
                "doctor_id"=> 2,
            ]]);
    }
}
