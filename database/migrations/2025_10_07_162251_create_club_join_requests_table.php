<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubJoinRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('club_join_requests', function (Blueprint $table) {
            $table->id();

            // CLB và người nộp đơn
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Trạng thái quy trình xin tham gia CLB
            $table->enum('status', [
                'pending_interview',
                'waiting_attendance',
                'waiting_approval',
                'approved',
                'rejected',
                'cancelled'    // 🚫 Sinh viên rút đơn hoặc CLB hủy
            ])->default('pending_interview');


            // Ghi chú và người xử lý
            $table->text('note')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');

            // Thời gian các mốc
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('handled_at')->nullable(); // ✅ thời gian duyệt/xử lý
            $table->timestamp('interview_scheduled_at')->nullable(); // ✅ lịch phỏng vấn (nếu có)
            $table->timestamps();
        });

    }



    public function down()
    {
        Schema::dropIfExists('club_join_requests');
    }
}