<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('estimated_cost', 12, 2)->nullable()->after('created_by');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('estimated_cost');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['estimated_cost', 'hourly_rate']);
        });
    }
};
