<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_entries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('username')->nullable();
            $table->text('password_encrypted'); // Crypt::encryptString
            $table->string('url')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_entries');
    }
};
