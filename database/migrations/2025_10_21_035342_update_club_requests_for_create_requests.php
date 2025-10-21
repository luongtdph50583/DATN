<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('club_requests', function (Blueprint $table) {
            // Nếu tồn tại cột club_id thì xóa
            if (Schema::hasColumn('club_requests', 'club_id')) {
                $table->dropConstrainedForeignId('club_id');
            }

            // Thêm các cột mô tả CLB (nếu chưa có)
            if (!Schema::hasColumn('club_requests', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('club_requests', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('club_requests', 'field')) {
                $table->string('field')->nullable();
            }
            if (!Schema::hasColumn('club_requests', 'status')) {
                $table->string('status')->default('pending');
            }
        });
    }

    public function down(): void
    {
        Schema::table('club_requests', function (Blueprint $table) {
            //
        });
    }
};

