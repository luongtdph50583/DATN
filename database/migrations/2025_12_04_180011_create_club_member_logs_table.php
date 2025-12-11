<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('club_member_logs', function (Blueprint $table) {
            $table->id();

            // Liên kết CLB
            $table->foreignId('club_id')
                ->constrained()
                ->onDelete('cascade')
                ->comment('Liên kết đến CLB');

            // Liên kết thành viên
            $table->foreignId('member_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Liên kết đến thành viên');

            // Hành động trong vòng đời thành viên
            $table->enum('action', [
                'joined',             // Tham gia CLB
                'appointed',          // Được bổ nhiệm vào ban quản lý
                'requested_leave',    // Gửi yêu cầu rời CLB
                'left',               // Chính thức rời CLB
                'kicked',             // Bị buộc rời CLB
                'violation_recorded'  // Ghi nhận vi phạm/nghĩa vụ chưa hoàn tất
            ])->comment('Hành động liên quan đến thành viên');

            // Người thực hiện hành động (có thể null nếu hệ thống tự động)
            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->comment('Người thực hiện hành động, null nếu hệ thống tự động');

            // Lý do hoặc chi tiết hành động
            $table->text('reason')->nullable()->comment('Chi tiết lý do hoặc ghi chú cho hành động');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_member_logs');
    }
};
