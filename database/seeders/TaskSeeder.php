<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Question;
use App\Models\Option;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $doctor = Doctor::first();
        $patients = Patient::take(3)->pluck('id');

        // ✅ 1. Multiple Choice Task
        $multiTask = Task::create([
            'name' => 'اختبار اختيار متعدد',
            'type' => 'multiple_choice',
            'doctor_id' => $doctor->id,
            'task_points' => 10,
            'target_date' => now()->addDays(2)
        ]);

        $multiQuestions = [
            [
                'question_text' => 'ما هو لون البحر؟',
                'options' => ['أزرق', 'أخضر', 'شفاف']
            ],
            [
                'question_text' => 'أين تقع القاهرة؟',
                'options' => ['مصر', 'السعودية', 'الأردن']
            ],
        ];

        foreach ($multiQuestions as $q) {
            $question = $multiTask->questions()->create([
                'question_text' => $q['question_text']
            ]);

            foreach ($q['options'] as $opt) {
                $question->options()->create(['text' => $opt]);
            }
        }

        $multiTask->patients()->attach($patients);

        // ✅ 2. Yes/No Task
        $yesNoTask = Task::create([
            'name' => 'مهمة نعم/لا',
            'type' => 'yes_no',
            'doctor_id' => $doctor->id,
            'task_points' => 5,
            'target_date' => now()->addDays(1)
        ]);

        $yesNoQuestions = [
            'هل تناولت الإفطار اليوم؟',
            'هل نمت 8 ساعات؟',
        ];

        foreach ($yesNoQuestions as $text) {
            $yesNoTask->questions()->create([
                'question_text' => $text
            ]);
        }

        $yesNoTask->patients()->attach($patients);

        // ✅ 3. Timer Task
        $timerTask = Task::create([
            'name' => 'مهمة مؤقت (تايمر)',
            'type' => 'timer',
            'doctor_id' => $doctor->id,
            'task_points' => 15,
            'target_date' => now()->addDays(4)
        ]);

        $timerQuestions = [
            ['question_text' => 'كم ثانية تستغرق لحل 2+2؟', 'time_seconds' => 10],
            ['question_text' => 'كم ثانية لقراءة هذه الجملة؟', 'time_seconds' => 8],
        ];

        foreach ($timerQuestions as $q) {
            $timerTask->questions()->create([
                'question_text' => $q['question_text'],
                'time_seconds' => $q['time_seconds']
            ]);
        }

        $timerTask->patients()->attach($patients);
    }
}
