<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fund_transactions', function (Blueprint $table) {
            // 🔹 Chỉ thêm cột source_id
            if (!Schema::hasColumn('fund_transactions', 'source_id')) {
                $table->uuid('source_id')->nullable()->after('id');

                $table->foreign('source_id')
                    ->references('id')
                    ->on('fund_sources')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fund_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('fund_transactions', 'source_id')) {
                $table->dropForeign(['source_id']);
                $table->dropColumn('source_id');
            }
        });
    }
};
