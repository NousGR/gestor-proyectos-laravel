<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'project_id',
        'is_completed',
        'due_date',
        'priority',
        'status',
        'order_column',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class)->latest();
    }
}
