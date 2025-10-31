<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_join_requests', function (Blueprint $table) {
            $table->text('reason')
                  ->nullable()
                  ->after('user_id')
                  ->collation('utf8mb4_unicode_ci');
        });
    }

    public function down(): void
    {
        Schema::table('club_join_requests', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};