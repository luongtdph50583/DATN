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

            $table->string('name')->comment('Tên CLB đề xuất');
            $table->text('description')->nullable()->comment('Mô tả CLB');
            $table->string('field')->nullable()->comment('Lĩnh vực hoạt động');

            // Thông tin liên hệ (nếu người tạo CLB cung cấp)
            $table->string('email')->nullable()->comment('Email liên hệ');
            $table->string('phone')->nullable()->comment('Số điện thoại liên hệ');
            $table->string('logo')->nullable()->comment('Logo đề xuất');

            // Trạng thái & người xử lý
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->comment('Trạng thái xử lý yêu cầu');
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Người duyệt yêu cầu');

            $table->text('note')->nullable()->comment('Ghi chú của người duyệt');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_requests');
    }
};
