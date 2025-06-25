<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        $project->load([
            'tasks' => function ($query) use ($request) {
                $query->orderBy('order_column', 'asc');
                if ($request->filled('filter_priority')) {
                    $query->where('priority', $request->filter_priority);
                }
                if ($request->filled('filter_status')) {
                    $query->where('status', $request->filter_status);
                }
                if ($request->filled('sort')) {
                    $sortField = $request->sort;
                    if ($sortField === 'priority') {
                        $query->orderByRaw("
                            CASE
                                WHEN priority = 'high' THEN 1
                                WHEN priority = 'medium' THEN 2
                                WHEN priority = 'low' THEN 3
                            END
                        ");
                    } else {
                        $query->orderBy($sortField, 'asc');
                    }
                }
            },
            'tasks.prerequisites',
            'tasks.dependents',
            'tasks.comments.user',
            'tasks.attachments'
        ]);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'filters' => $request->only(['filter_priority', 'filter_status', 'sort']),
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
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('projects', 'public');
            $validated['image_url'] = $path;
        }

        unset($validated['image']);

        $request->user()->projects()->create($validated);
        return Redirect::route('dashboard')->with('success', 'Proyecto creado con éxito.');
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $project->title = $validatedData['title'];
        $project->description = $validatedData['description'];
        $project->color = $validatedData['color'];
        $project->icon = $validatedData['icon'];

        if ($request->boolean('remove_image') && $project->image_url) {
            Storage::disk('public')->delete($project->image_url);
            $project->image_url = null;
        }

        if ($request->hasFile('image')) {
            if ($project->image_url) {
                Storage::disk('public')->delete($project->image_url);
            }
            $path = $request->file('image')->store('projects', 'public');
            $project->image_url = $path;
        }

        $project->save();

        return Redirect::route('dashboard')->with('success', 'Proyecto actualizado con éxito.');
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
