<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_fund_requests', function (Blueprint $table) {
            $table->decimal('amount_disbursed', 15, 2)->default(0)->after('approved_amount');
            $table->date('disbursement_start')->nullable()->after('amount_disbursed');
            $table->date('disbursement_end')->nullable()->after('disbursement_start');
        });
    }

    public function down(): void
    {
        Schema::table('event_fund_requests', function (Blueprint $table) {
            $table->dropColumn(['amount_disbursed', 'disbursement_start', 'disbursement_end']);
        });
    }
};
