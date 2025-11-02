<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubLeaveRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('club_leave_requests', function (Blueprint $table) {
            $table->id(); // Khóa chính

            // Liên kết CLB & người gửi yêu cầu
            $table->foreignId('club_id')
                ->constrained()
                ->onDelete('cascade')
                ->comment('CLB mà người dùng muốn rời khỏi');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Người gửi yêu cầu rời CLB');

            // Trạng thái duyệt yêu cầu
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->comment('Trạng thái xử lý yêu cầu');

            // Người xử lý yêu cầu
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Người duyệt yêu cầu rời CLB');

            $table->timestamp('requested_at')
                ->useCurrent()
                ->comment('Thời điểm gửi yêu cầu');

            $table->timestamp('handled_at')
                ->nullable()
                ->comment('Thời điểm xử lý yêu cầu');

            // Lý do & ghi chú
            $table->text('reason')->nullable()->comment('Lý do rời CLB');
            $table->text('note')->nullable()->comment('Ghi chú của người duyệt (nếu có)');

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('club_leave_requests');
    }
}
