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

            // Thông tin liên hệ
            $table->string('email')->nullable()->comment('Email liên hệ');
            $table->string('phone')->nullable()->comment('Số điện thoại liên hệ');
            $table->string('logo')->nullable()->comment('Logo đề xuất');

            // Giảng viên đỡ đầu
            $table->foreignId('advisor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Giảng viên đỡ đầu được mời');

            $table->enum('advisor_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->comment('Trạng thái chấp thuận của giảng viên đỡ đầu');

            // Trạng thái xử lý yêu cầu & người duyệt
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->comment('Trạng thái xử lý yêu cầu');
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Người duyệt yêu cầu');

            $table->text('note')->nullable()->comment('Ghi chú của người duyệt');


            // 🔹 Thêm rule và member_limit
            $table->text('rule')->nullable()->comment('Quy tắc của CLB');
            $table->integer('member_limit')->nullable()->comment('Số lượng thành viên tối đa');

            // 🔹 Thêm type để phân biệt loại yêu cầu
           
            $table->timestamps();
        });

    }



    public function down(): void
    {
        Schema::dropIfExists('club_requests');
    }
};
