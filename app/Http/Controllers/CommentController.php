<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('create', [Comment::class, $task]);

        $request->validate([
            'body' => 'required|string',
        ]);

        $task->comments()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return Redirect::back()->with('success', 'Comentario añadido.');
    }
}
