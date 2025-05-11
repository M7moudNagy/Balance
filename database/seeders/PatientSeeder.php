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
        Patient::insert([
            [
                "fullname"=> "Ahmed Mohamed",
                "nickname"=> "Ali",
                "phoneNumber" => "01099594809",
                "age"=> "22",
                "password"=> bcrypt("123456789"),
                "gander"=> "Male",
                "city"=> "القاهره",
                "email"=> "ahmed001@example.com",
            ],
            [
                "fullname"=> "Mohamed Tarek",
                "nickname"=> "Mo",
                "phoneNumber" => "01099594809",
                "age"=> "25",
                "password"=> bcrypt("123456789"),
                "gander"=> "Male",
                "city"=> "الجيزة",
                "email"=> "mohamed002@example.com",
            ],
            [
                "fullname"=> "Ali Hassan",
                "nickname"=> "Hassouna",
                "phoneNumber" => "01099594809",
                "age"=> "28",
                "password"=> bcrypt("123456789"),
                "gander"=> "Male",
                "city"=> "القليوبية",
                "email"=> "ali003@example.com",
            ],
            [
                "fullname"=> "Sara Adel",
                "nickname"=> "Soso",
                "phoneNumber" => "01099594809",
                "age"=> "20",
                "password"=> bcrypt("123456789"),
                "gander"=> "Female",
                "city"=> "المنصورة",
                "email"=> "sara004@example.com",
            ],
            [
                "fullname"=> "Laila Ahmed",
                "nickname"=> "Lilo",
                "phoneNumber" => "01099594809",
                "age"=> "23",
                "password"=> bcrypt("123456789"),
                "gander"=> "Female",
                "city"=> "الاسكندرية",
                "email"=> "laila005@example.com",
            ],
            [
                "fullname"=> "Hossam Gamal",
                "nickname"=> "Sam",
                "phoneNumber" => "01099594809",
                "age"=> "26",
                "password"=> bcrypt("123456789"),
                "gander"=> "Male",
                "city"=> "أسيوط",
                "email"=> "hossam006@example.com",
            ],
            [
                "fullname"=> "Nour Hassan",
                "nickname"=> "Nour",
                "phoneNumber" => "01099594809",
                "age"=> "21",
                "password"=> bcrypt("123456789"),
                "gander"=> "Female",
                "city"=> "القاهرة",
                "email"=> "nour007@example.com",
            ],
            [
                "fullname"=> "Karim Said",
                "nickname"=> "Kimo",
                "phoneNumber" => "01099594809",
                "age"=> "29",
                "password"=> bcrypt("123456789"),
                "gander"=> "Male",
                "city"=> "بني سويف",
                "email"=> "karim008@example.com",
            ],
            [
                "fullname"=> "Salma Yasser",
                "nickname"=> "Sally",
                "phoneNumber" => "01099594809",
                "age"=> "24",
                "password"=> bcrypt("123456789"),
                "gander"=> "Female",
                "city"=> "السويس",
                "email"=> "salma009@example.com",
            ],
            [
                "fullname"=> "Tamer Fathy",
                "nickname"=> "Timo",
                "phoneNumber" => "01099594809",
                "age"=> "30",
                "password"=> bcrypt("123456789"),
                "gander"=> "Male",
                "city"=> "الشرقية",
                "email"=> "tamer010@example.com",
            ],
        ]);
        
    }
}
