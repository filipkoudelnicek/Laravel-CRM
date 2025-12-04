<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['todo', 'in_progress', 'pending_approval', 'done'])->default('todo');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
            $table->dateTime('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

