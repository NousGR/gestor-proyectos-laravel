<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
        ]);

        $lastOrder = $project->tasks()->max('order_column');
        $validated['order_column'] = $lastOrder + 1;

        $project->tasks()->create($validated);

        return Redirect::route('projects.show', $project)->with('success', 'Tarea añadida con éxito.');
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'is_completed' => 'sometimes|required|boolean',
            'due_date' => 'sometimes|nullable|date',
            'priority' => ['sometimes','required', Rule::in(['low', 'medium', 'high'])],
        ]);

        $task->update($validated);

        return Redirect::back(); // Usamos Redirect::back() para que funcione desde cualquier vista
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return Redirect::route('projects.show', $task->project_id)->with('success', 'Tarea eliminada.');
    }
}
