<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Doctor::insert([[
        "first_name"=> "mahmoud",
        "last_name"=> "nagy",
        "email"=> "mahmoudnagy4809@gmail.com",
        "password"=> bcrypt("123456789"),
        "phone_number"=> "01099594809",
        "gender"=> "male",
        "date_of_birth"=> "2003-06-22",
        "address"=> "123 شارع النصر, القاهرة",
        "governorate"=> "القاهرة",
        "medical_specialty"=> "Cardiology",
        "years_of_experience"=> "10",
        "type_of_practice"=> "Private",
        "facility_name"=> "Heart Care Clinic",
        "facility_address"=> "456 شارع الثورة, الجيزة",
        "facility_governorate"=> "الجيزة",
        "medical_license_number"=> "ML123456789",
        "medical_license"=> "uploads/doctors/NmcDdEmOZA0S5LveEs8vIbwciqUjA7x1dhmLnL4Z.jpg",
        "graduation_certificate"=> "uploads/doctors/dDWxU6pNorqnmiFT1Ofep2P0mvBzEwPWVw02UuBD.jpg",
        "national_id_or_passport"=> "uploads/doctors/7AlAsPWi3BGWEPzLOZGZ9MHfzRXynXW78znH3wfb.jpg",
        "motivation"=> "أرغب في مساعدة المرضى وتحسين الرعاية الصحية.",
        "balance_help"=> "أساهم في توعية المجتمع بأمراض القلب.",
        "licensed_provider"=> true,
        "agree_terms"=> true,
        "updated_at"=> now(),
        "created_at"=> now(),
        ],
            [
                "first_name"=> "mostafa",
                "last_name"=> "mnesey",
                "email"=> "mnesey@gmail.com",
                "password"=> bcrypt("123456789"),
                "phone_number"=> "011222256789",
                "gender"=> "male",
                "date_of_birth"=> "1985-06-15",
                "address"=> "123 شارع النصر, القاهرة",
                "governorate"=> "القاهرة",
                "medical_specialty"=> "Cardiology",
                "years_of_experience"=> "10",
                "type_of_practice"=> "Private",
                "facility_name"=> "Heart Care Clinic",
                "facility_address"=> "456 شارع الثورة, الجيزة",
                "facility_governorate"=> "الجيزة",
                "medical_license_number"=> "ML1230056789",
                "medical_license"=> "uploads/doctors/NmcDdEmOZA0S5LveEs8vIbwciqUjA7x1dhmLnL4Z.jpg",
                "graduation_certificate"=> "uploads/doctors/dDWxU6pNorqnmiFT1Ofep2P0mvBzEwPWVw02UuBD.jpg",
                "national_id_or_passport"=> "uploads/doctors/7AlAsPWi3BGWEPzLOZGZ9MHfzRXynXW78znH3wfb.jpg",
                "motivation"=> "أرغب في مساعدة المرضى وتحسين الرعاية الصحية.",
                "balance_help"=> "أساهم في توعية المجتمع بأمراض القلب.",
                "licensed_provider"=> true,
                "agree_terms"=> true,
                "updated_at"=> now(),
                "created_at"=> now(),
            ]]);
    }
}
