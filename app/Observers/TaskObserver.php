<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    public function created(Task $task): void
    {
        Activity::create([
            'project_id'   => $task->project_id,
            'user_id'      => $task->project->user_id, // <-- Cambio aquí
            'description'  => 'created_task',
            'subject_id'   => $task->id,
            'subject_type' => Task::class,
        ]);
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('is_completed') && $task->is_completed) {
            Activity::create([
                'project_id'   => $task->project_id,
                'user_id'      => $task->project->user_id, // <-- Cambio aquí
                'description'  => 'completed_task',
                'subject_id'   => $task->id,
                'subject_type' => Task::class,
            ]);
        }
    }

    public function deleted(Task $task): void
    {
        $task->activities()->delete();
    }

    public function restored(Task $task): void
    {
        //
    }

    public function forceDeleted(Task $task): void
    {
        //
    }
}
