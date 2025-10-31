<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_requests', function (Blueprint $table) {
            $table->string('email', 255)
                  ->nullable()
                  ->after('field')
                  ->collation('utf8mb4_unicode_ci');

            $table->string('phone', 20)
                  ->nullable()
                  ->after('email')
                  ->collation('utf8mb4_unicode_ci');

            $table->string('logo', 255)
                  ->nullable()
                  ->after('phone')
                  ->collation('utf8mb4_unicode_ci');
        });
    }

    public function down(): void
    {
        Schema::table('club_requests', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'logo']);
        });
    }
};