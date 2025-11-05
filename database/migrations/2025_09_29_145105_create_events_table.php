<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id(); // id: bigint, AUTO_INCREMENT, PRIMARY KEY

            // Liên kết với CLB
            $table->foreignId('club_id')
                  ->constrained('clubs')
                  ->onDelete('cascade')
                  ->comment('CLB tổ chức sự kiện');

            // Thông tin cơ bản
            $table->string('name', 255)->comment('Tên sự kiện');
            $table->text('description')->nullable()->comment('Mô tả chi tiết');
            $table->dateTime('start_time')->nullable()->comment('Thời gian bắt đầu');
            $table->dateTime('end_time')->nullable()->comment('Thời gian kết thúc');
            $table->string('location', 255)->comment('Địa điểm tổ chức');

            // Giới hạn người tham gia
            $table->integer('max_participants')->nullable()->comment('Giới hạn số người tham gia');

            // Công khai / nội bộ
            $table->tinyInteger('is_public')
                  ->default(1)
                  ->comment('Công khai hoặc nội bộ');

            // Trạng thái duyệt
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->comment('Trạng thái duyệt sự kiện');

            // Người tạo và duyệt
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Người tạo sự kiện');

            $table->foreignId('approval_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Người duyệt sự kiện');

            // Media liên kết
            $table->foreignId('media_id')
                  ->nullable()
                  ->constrained('media')
                  ->onDelete('set null')
                  ->comment('Hình ảnh / video sự kiện');

            // Ngân sách chi tiết
            $table->decimal('budget_estimated', 15, 2)
                  ->default(0.00)
                  ->comment('Ngân sách dự kiến');

            $table->decimal('budget_current', 15, 2)
                  ->default(0.00)
                  ->comment('Ngân sách hiện tại');

            $table->decimal('budget_used', 15, 2)
                  ->default(0.00)
                  ->comment('Ngân sách đã sử dụng');


            // Timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};