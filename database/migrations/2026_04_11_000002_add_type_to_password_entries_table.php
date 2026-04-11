<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_entries', function (Blueprint $table) {
            $table->enum('type', ['general', 'sftp', 'admin', 'hosting'])
                  ->default('general')
                  ->after('password_encrypted');
        });
    }

    public function down(): void
    {
        Schema::table('password_entries', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
