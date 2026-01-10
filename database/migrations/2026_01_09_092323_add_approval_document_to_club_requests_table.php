<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('club_requests', function (Blueprint $table) {
            $table->string('approval_document')
                ->nullable()
                ->after('plan')
                ->comment('Giấy tờ có chữ ký đồng ý thành lập CLB');
        });
    }

    public function down(): void
    {
        Schema::table('club_requests', function (Blueprint $table) {
            $table->dropColumn('approval_document');
        });
    }
};
