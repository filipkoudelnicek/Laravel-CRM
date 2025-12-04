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
            $table->string('username');
            $table->text('password'); // Encrypted password
            $table->string('url')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_entries');
    }
};

