<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\Task;
use App\Models\User;

class AttachmentPolicy
{
    public function create(User $user, Task $task): bool
    {
        return $user->id === $task->project->user_id;
    }

    public function view(User $user, Attachment $attachment): bool
    {
        return $user->id === $attachment->task->project->user_id;
    }

    public function delete(User $user, Attachment $attachment): bool
    {
        return $user->id === $attachment->user_id || $user->id === $attachment->task->project->user_id;
    }
}
