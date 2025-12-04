<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_entry_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('password_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['password_entry_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_entry_users');
    }
};

