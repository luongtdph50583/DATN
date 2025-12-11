<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('club_requests', function (Blueprint $table) {
            $table->id();

            // Người gửi yêu cầu (người đề xuất thành lập CLB)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Người đề xuất');

            // Thông tin cơ bản về CLB
            $table->string('name')->comment('Tên CLB đề xuất');
            $table->string('slogan')->nullable()->comment('Khẩu hiệu của CLB');
            $table->text('description')->nullable()->comment('Mô tả CLB');
            $table->text('purpose')->nullable()->comment('Mục đích hoạt động của CLB');
            $table->string('field')->nullable()->comment('Lĩnh vực hoạt động');

            // Kế hoạch hoạt động 3 tháng đầu
            $table->text('plan')->nullable()->comment('Kế hoạch hoạt động trong 3 tháng đầu');

            // 🔹 THÊM: File kế hoạch 3 tháng (bắt buộc theo form)
            $table->string('plan_file')->nullable()->comment('File kế hoạch 3 tháng đầu (PDF/DOC)');

            // Thông tin liên hệ
            $table->string('email')->nullable()->comment('Email liên hệ');
            $table->string('phone')->nullable()->comment('Số điện thoại liên hệ');
            $table->string('logo')->nullable()->comment('Logo đề xuất');

            // 🔹 THÊM: Ban chủ nhiệm (6 vị trí quan trọng)
            $table->foreignId('club_manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Chủ nhiệm CLB');

            $table->foreignId('deputy_manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Phó chủ nhiệm CLB');

            $table->foreignId('secretary_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Thư ký CLB');

            $table->foreignId('treasurer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Thủ quỹ CLB');

            $table->foreignId('event_manager_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Quản lý sự kiện CLB');

            $table->foreignId('communication_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Phụ trách truyền thông CLB');

            // Giảng viên đỡ đầu (giữ nguyên - có thể dùng sau)
            // $table->foreignId('advisor_id')
            //     ->nullable()
            //     ->constrained('users')
            //     ->nullOnDelete()
            //     ->comment('Giảng viên phụ trách được mời');

            // $table->enum('advisor_status', ['pending', 'approved', 'rejected'])
            //     ->default('pending')
            //     ->comment('Trạng thái chấp thuận của giảng viên phụ trách');

            // 🔹 CẢI TIẾN: Trạng thái xử lý yêu cầu (thêm 'confirmed')
            $table->enum('status', ['pending', 'confirmed', 'approved', 'rejected'])
                ->default('pending')
                ->comment('Trạng thái: pending=chờ xác nhận thành viên, confirmed=đã xác nhận đủ, approved=admin đã duyệt, rejected=bị từ chối');

            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin duyệt yêu cầu');

            $table->text('note')->nullable()->comment('Ghi chú của người duyệt');

            // 🔹 THÊM: Lý do từ chối (riêng biệt với note)
            $table->text('rejection_reason')->nullable()->comment('Lý do từ chối cụ thể');

            // Quy tắc và giới hạn thành viên
            $table->text('rule')->nullable()->comment('Quy tắc của CLB');
            $table->integer('member_limit')->nullable()->comment('Số lượng thành viên tối đa');

            // 🔹 THÊM: Thông tin PDF đã sinh
            $table->string('pdf_file')->nullable()->comment('Đường dẫn file PDF đơn đã tạo');
            $table->timestamp('pdf_generated_at')->nullable()->comment('Thời điểm tạo PDF');

            // 🔹 THÊM: Timestamp phê duyệt/từ chối
            $table->timestamp('approved_at')->nullable()->comment('Thời điểm phê duyệt');
            $table->timestamp('rejected_at')->nullable()->comment('Thời điểm từ chối');

            $table->timestamps();
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('club_requests');
    }
};
