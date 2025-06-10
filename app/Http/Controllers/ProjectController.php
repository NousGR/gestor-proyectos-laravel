<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        return Inertia::render('Projects/Show', [
            'project' => $project->load('tasks')
        ]);
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        return Inertia::render('Projects/Edit', [
            'project' => $project,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $validated['image_url'] = $path;
        }

        // Eliminamos la clave 'image' del array validado antes de crear el registro
        unset($validated['image']);

        $request->user()->projects()->create($validated);
        return Redirect::route('dashboard')->with('success', 'Proyecto creado con éxito.');
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);
        return Redirect::route('projects.show', 'project')->with('success', 'Proyecto actualizado con éxito.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        if ($project->image_url) {
            Storage::disk('public')->delete($project->image_url);
        }

        $project->delete();
        return Redirect::route('dashboard')->with('success', 'Proyecto eliminado con éxito.');
    }
}
