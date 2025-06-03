<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;
use App\Notifications\SessionReminderNotification;

class SendSessionReminder extends Command
{
    // protected $signature = 'app:send-session-reminder';
    // protected $description = 'Command description';
    // public function handle()
    // {
    //     //
    // }

     protected $signature = 'sessions:send-reminder';
    protected $description = 'Send reminders 15 minutes before session';

    public function handle()
    {
        $targetTime = now()->addMinutes(15)->format('H:i:00');

        Session::where('status', 'upcoming')
            ->where('date', today())
            ->where('time', $targetTime)
            ->with(['patient', 'doctor'])
            ->get()
            ->each(function ($session) {
                $session->patient->notify(new SessionReminderNotification($session));
                $session->doctor->notify(new SessionReminderNotification($session));
            });

        $this->info('Session reminders sent.');
    }
}
