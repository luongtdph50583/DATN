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
    Schema::table('event_fund_settlements', function (Blueprint $table) {
        $table->unsignedBigInteger('fund_request_id')->after('id');

        $table->foreign('fund_request_id')
              ->references('id')
              ->on('event_fund_requests')
              ->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('event_fund_settlements', function (Blueprint $table) {
        $table->dropForeign(['fund_request_id']);
        $table->dropColumn('fund_request_id');
    });
}

};
