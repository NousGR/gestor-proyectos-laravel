<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TaskOrderController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*' => 'integer|exists:tasks,id',
        ]);

        $project = Task::find($request->tasks[0])->project;
        $this->authorize('update', $project);

        foreach ($request->tasks as $index => $taskId) {
            Task::where('id', $taskId)->update(['order_column' => $index]);
        }

        return Redirect::back();
    }
}
