<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fund_transactions', function (Blueprint $table) {
            // Số tiền thực tế đã thu/chi
            if (!Schema::hasColumn('fund_transactions', 'collected_amount')) {
                $table->decimal('collected_amount', 15, 2)->nullable()->after('amount')->comment('Số tiền thực tế thu/chi');
            }

            // Danh mục tùy chỉnh, nếu muốn nhập ngoài category hiện có
            if (!Schema::hasColumn('fund_transactions', 'custom_category')) {
                $table->string('custom_category')->nullable()->after('category')->comment('Danh mục tùy chỉnh nếu không dùng category chuẩn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fund_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('fund_transactions', 'collected_amount')) {
                $table->dropColumn('collected_amount');
            }
            if (Schema::hasColumn('fund_transactions', 'custom_category')) {
                $table->dropColumn('custom_category');
            }
        });
    }
};
