<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('paused_duration')->default(0); // Total paused time in seconds
            $table->enum('status', ['running', 'paused', 'stopped'])->default('running');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_time_entries');
    }
};

