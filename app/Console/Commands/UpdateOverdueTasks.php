<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PatientTask;
use Carbon\Carbon;

class UpdateOverdueTasks extends Command
{
    protected $signature = 'tasks:update-overdue';
    protected $description = 'Mark tasks as over_due if target_date passed and not completed';

    public function handle()
    {
        $now = Carbon::now();

        // هات كل المهام اللي عدّى وقتها ولسه مش مكتملة
        $overdueTasks = PatientTask::whereHas('task', function ($query) use ($now) {
            $query->where('target_date', '<', $now);
        })->where('status', '!=', 'completed')->get();

        foreach ($overdueTasks as $patientTask) {
            $patientTask->status = 'over_due';
            $patientTask->save();
        }

        $this->info('Overdue tasks updated successfully.');
    }
}
