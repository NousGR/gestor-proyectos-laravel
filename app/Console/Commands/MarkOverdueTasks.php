<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MarkOverdueTasks extends Command
{
    protected $signature = 'tasks:mark-overdue';

    protected $description = 'Mark tasks as overdue if their due date has passed and they are not completed';

    public function handle()
    {
        $this->info('Checking for overdue tasks...');

        $overdueTasks = Task::where('is_completed', false)
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::today())
            ->where('status', 'pending')
            ->get();

        if ($overdueTasks->isEmpty()) {
            $this->info('No pending tasks are overdue. All good!');
            return;
        }

        $count = $overdueTasks->count();

        foreach ($overdueTasks as $task) {
            $task->status = 'overdue';
            $task->save();
        }

        $this->info("Successfully marked {$count} task(s) as overdue.");
    }
}
