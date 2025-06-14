<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TaskDependencyController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('update', $task->project);

        $request->validate([
            'prerequisite_task_id' => 'required|exists:tasks,id',
        ]);

        $task->prerequisites()->attach($request->prerequisite_task_id);

        return Redirect::back()->with('success', 'Dependencia añadida.');
    }

    public function destroy(Task $task, Task $prerequisite)
    {
        $this->authorize('update', $task->project);

        $task->prerequisites()->detach($prerequisite->id);

        return Redirect::back()->with('success', 'Dependencia eliminada.');
    }
}
