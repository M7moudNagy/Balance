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
        Doctor::insert([
            [
                "fullname" => "mahmoud nagy",
                "phone_number" => "01099594809",
                "email" => "mahmoudnagy4809@gmail.com",
                "password" => bcrypt("123456789"),
                "specialization" => "Cardiology",
                "medical_license_number" => "ML123456789",
                "years_of_experience" => 10,
                "clinic_or_hospital_name" => "Heart Care Clinic",
                "work_address" => "456 شارع الثورة, الجيزة",
                "available_working_hours" => "Saturday to Thursday, 9AM to 5PM",
                "gender" => "male",
                "bio"=>"mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm",
                "image" => "uploads/doctors/image1.png",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "fullname" => "mostafa mnesey",
                "phone_number" => "011222256789",
                "email" => "mnesey@gmail.com",
                "password" => bcrypt("123456789"),
                "specialization" => "Cardiology",
                "medical_license_number" => "ML1230056789",
                "years_of_experience" => 10,
                "clinic_or_hospital_name" => "Heart Care Clinic",
                "work_address" => "456 شارع الثورة, الجيزة",
                "available_working_hours" => "Saturday to Thursday, 9AM to 5PM",
                "gender" => "male",
                "bio"=>"mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm",
                "image" => "uploads/doctors/image2.png",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "fullname" => "sara abdallah",
                "phone_number" => "01234567891",
                "email" => "sara.abdallah@example.com",
                "password" => bcrypt("123456789"),
                "specialization" => "Dermatology",
                "medical_license_number" => "ML1122334455",
                "years_of_experience" => 7,
                "clinic_or_hospital_name" => "SkinCare Center",
                "work_address" => "شارع جمال عبد الناصر, القاهرة",
                "available_working_hours" => "Sunday to Thursday, 10AM to 4PM",
                "gender" => "female",
                "bio"=>"mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm",
                "image" => "uploads/doctors/image3.png",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "fullname" => "ahmed kamal",
                "phone_number" => "01011112222",
                "email" => "ahmed.kamal@example.com",
                "password" => bcrypt("123456789"),
                "specialization" => "Neurology",
                "medical_license_number" => "ML5566778899",
                "years_of_experience" => 12,
                "clinic_or_hospital_name" => "Brain Health Clinic",
                "work_address" => "شارع جامعة الدول العربية, الجيزة",
                "available_working_hours" => "Monday to Friday, 8AM to 3PM",
                "gender" => "male",
                "bio"=>"mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm",
                "image" => "uploads/doctors/image4.png",
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "fullname" => "mona elmasry",
                "phone_number" => "01033334444",
                "email" => "mona.elmasry@example.com",
                "password" => bcrypt("123456789"),
                "specialization" => "Psychiatry",
                "medical_license_number" => "ML9988776655",
                "years_of_experience" => 9,
                "clinic_or_hospital_name" => "Mind Care Center",
                "work_address" => "شارع التحرير, الدقي",
                "available_working_hours" => "Saturday to Wednesday, 11AM to 6PM",
                "gender" => "female",
                "bio"=>"mmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmm",
                "image" => "uploads/doctors/image5.png",
                "created_at" => now(),
                "updated_at" => now(),
            ]
        ]);
        
    }
}
