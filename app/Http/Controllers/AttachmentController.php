<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('create', [Attachment::class, $task]);

        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store("attachments/{$task->id}", 'local');

        $task->attachments()->create([
            'user_id' => $request->user()->id,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return Redirect::back()->with('success', 'Archivo adjuntado.');
    }

    public function show(Attachment $attachment)
    {
        $this->authorize('view', $attachment);

        $path = Storage::disk('local')->path($attachment->path);

        return response()->download($path, $attachment->original_name);
    }

    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        Storage::disk('local')->delete($attachment->path);
        $attachment->delete();

        return Redirect::back()->with('success', 'Archivo eliminado.');
    }
}
