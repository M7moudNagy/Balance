<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;

class MarkMissedSessions extends Command
{
    // protected $signature = 'app:mark-missed-sessions';
    // protected $description = 'Command description';
    // public function handle()
    // {
    //     //
    // }

    protected $signature = 'sessions:mark-missed';
    protected $description = 'Mark sessions as missed if time passed and not completed';

    public function handle()
    {
        Session::where('status', 'upcoming')
            ->where(function ($query) {
                $query->where('date', '<', today())
                      ->orWhere(function ($q) {
                          $q->where('date', today())
                            ->where('time', '<', now()->format('H:i:s'));
                      });
            })
            ->update(['status' => 'missed']);

        $this->info('Missed sessions marked.');
    }
}
