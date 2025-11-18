<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm các giá trị mới vào enum
        DB::statement("ALTER TABLE club_join_requests MODIFY COLUMN interview_result ENUM(
            'pending',
            'completed',
            'no_show',
            'cancelled',
            'pass',
            'fail'
        ) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Chuyển pass/fail về completed nếu cần
        DB::table('club_join_requests')
            ->whereIn('interview_result', ['pass', 'fail'])
            ->update(['interview_result' => 'completed']);

        // Xóa các giá trị mới
        DB::statement("ALTER TABLE club_join_requests MODIFY COLUMN interview_result ENUM(
            'pending',
            'completed',
            'no_show',
            'cancelled'
        ) DEFAULT 'pending'");
    }
};
