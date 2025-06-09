<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        return Inertia::render('Projects/Show', [
            'project' => $project->load('tasks')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $request->user()->projects()->create($validated);

        return Redirect::route('dashboard');
    }
}
