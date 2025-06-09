<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // <-- Añadido para manejar archivos

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
            'image' => 'nullable|image|max:2048', // <-- Validamos que sea una imagen
        ]);

        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('projects', 'public');
        }

        $request->user()->projects()->create($validated);
        return Redirect::route('dashboard')->with('success', 'Proyecto creado con éxito.');
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        // Aquí no manejaremos la actualización de imagen aún para mantenerlo simple.
        // Nos enfocaremos en el CRUD de la información del proyecto.
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);
        return Redirect::route('projects.show', $project->id)->with('success', 'Proyecto actualizado con éxito.');
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
