<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Session;
use App\Models\SessionTemplate;
use Illuminate\Console\Command;

class GenerateDailySessions extends Command
{
    // protected $signature = 'app:generate-daily-sessions';
    // protected $description = 'Command description';
     protected $signature = 'sessions:generate-daily';
    protected $description = 'Generate today\'s sessions from templates';

    public function handle()
    {
        $today = Carbon::today();
        $dayName = $today->format('l');

        $templates = SessionTemplate::where('recurrence', 'weekly')
            ->whereDate('recurrence_end_date', '>=', $today)
            ->get();

        foreach ($templates as $template) {
            $days = $template->recurrence_days;

            if (in_array($dayName, $days)) {
                Session::firstOrCreate([
                    'doctor_id' => $template->doctor_id,
                    'patient_id' => $template->patient_id,
                    'date' => $today->toDateString(),
                    'time' => $template->time,
                    'platform_link' => $template->platform_link,
                    'status' => 'upcoming',
                ]);
            }
        }
        $this->info('Sessions generated successfully.');

    
    
    }
}
