<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Project;

class ProjectObserver
{
    public function created(Project $project): void
    {
        Activity::create([
            'project_id' => $project->id,
            'user_id' => $project->user_id, // <-- Lógica consistente
            'description' => 'created_project',
        ]);
    }

    public function updated(Project $project): void
    {
        // Placeholder para futuras actividades de actualización del proyecto
    }

    public function deleted(Project $project): void
    {
        //
    }

    public function restored(Project $project): void
    {
        //
    }

    public function forceDeleted(Project $project): void
    {
        //
    }
}
