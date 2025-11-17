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
    Schema::table('event_fund_requests', function (Blueprint $table) {
        $table->json('disbursement_history')->nullable()->after('disbursement_proof')
            ->comment('Lưu lịch sử giải ngân: mỗi lần gồm amount, proof[], date, disbursed_by');
    });
}

public function down(): void
{
    Schema::table('event_fund_requests', function (Blueprint $table) {
        $table->dropColumn('disbursement_history');
    });
}

};
