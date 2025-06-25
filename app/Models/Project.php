<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'color',
        'icon',
    ];

    protected $appends = ['progress'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    protected function progress(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $totalTasks = $this->tasks()->count();

                if ($totalTasks === 0) {
                    return 0;
                }

                $completedTasks = $this->tasks()->where('is_completed', true)->count();

                return round(($completedTasks / $totalTasks) * 100);
            }
        );
    }

    public function activities()
    {
        return $this->hasMany(Activity::class)->latest();
    }
}
