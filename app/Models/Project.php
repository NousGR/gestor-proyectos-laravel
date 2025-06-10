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
    ];

    // Le decimos a Laravel que siempre añada nuestro atributo 'progress'
    // cuando convierta el modelo a un array o JSON.
    protected $appends = ['progress'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // --- ESTA ES LA NUEVA FUNCIÓN (ACCESSOR) ---
    protected function progress(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $totalTasks = $this->tasks()->count();

                if ($totalTasks === 0) {
                    return 0; // Si no hay tareas, el progreso es 0
                }

                $completedTasks = $this->tasks()->where('is_completed', true)->count();

                // Calculamos y redondeamos el porcentaje
                return round(($completedTasks / $totalTasks) * 100);
            }
        );
    }
}
