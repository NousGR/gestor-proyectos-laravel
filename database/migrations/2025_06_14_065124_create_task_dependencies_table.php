<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->primary(['task_id', 'prerequisite_task_id']);

            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('prerequisite_task_id')->constrained('tasks')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};
