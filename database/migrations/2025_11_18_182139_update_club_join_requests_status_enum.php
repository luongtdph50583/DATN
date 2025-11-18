<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateClubJoinRequestsStatusEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Bước 1: Thêm các giá trị mới vào enum (giữ nguyên giá trị cũ)
        DB::statement("ALTER TABLE club_join_requests MODIFY COLUMN status ENUM(
            'pending',
            'scheduling_interview',
            'interview',
            'interview_completed',
            'pending_interview',
            'waiting_attendance',
            'waiting_approval',
            'approved',
            'rejected',
            'cancelled'
        ) DEFAULT 'pending'");

        // Bước 2: Cập nhật dữ liệu cũ sang giá trị mới
        DB::table('club_join_requests')
            ->where('status', 'pending')
            ->update(['status' => 'pending_interview']);
        
        DB::table('club_join_requests')
            ->whereIn('status', ['scheduling_interview', 'interview'])
            ->update(['status' => 'waiting_attendance']);
        
        DB::table('club_join_requests')
            ->where('status', 'interview_completed')
            ->update(['status' => 'waiting_approval']);

        // Bước 3: Xóa các giá trị cũ khỏi enum, chỉ giữ lại giá trị mới
        DB::statement("ALTER TABLE club_join_requests MODIFY COLUMN status ENUM(
            'pending_interview',
            'waiting_attendance',
            'waiting_approval',
            'approved',
            'rejected',
            'cancelled'
        ) DEFAULT 'pending_interview'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert về enum cũ
        DB::statement("ALTER TABLE club_join_requests MODIFY COLUMN status ENUM(
            'pending',
            'scheduling_interview',
            'interview',
            'interview_completed',
            'approved',
            'rejected',
            'cancelled'
        ) DEFAULT 'pending'");
    }
}
