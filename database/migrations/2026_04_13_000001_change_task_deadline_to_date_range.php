<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add new columns
            $table->date('starts_at')->nullable()->after('priority');
            $table->date('due_at')->nullable()->after('starts_at');
            
            // Drop old column
            $table->dropColumn('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('priority');
            $table->dropColumn(['starts_at', 'due_at']);
        });
    }
};
