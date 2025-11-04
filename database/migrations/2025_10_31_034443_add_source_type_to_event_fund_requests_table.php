<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_fund_requests', function (Blueprint $table) {
            $table->enum('source_type', ['school', 'sponsor', 'club'])
                  ->default('school')
                  ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('event_fund_requests', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
};
