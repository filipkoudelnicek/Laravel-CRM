<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 3)->default('CZK');
            $table->date('period_from');
            $table->date('period_to');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('period_to');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_plans');
    }
};
