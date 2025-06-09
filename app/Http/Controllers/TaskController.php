<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $project->tasks()->create($validated);

        return Redirect::route('projects.show', $project);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'is_completed' => $request->is_completed
        ]);

        return Redirect::route('projects.show', $task->project_id);
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return Redirect::route('projects.show', $task->project_id);
    }
}
