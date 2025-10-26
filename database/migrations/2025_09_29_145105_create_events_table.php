<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
   public function up()
{
    Schema::create('events', function (Blueprint $table) {
        $table->id(); // Khóa chính

        // Liên kết CLB tổ chức sự kiện
        $table->foreignId('club_id')
              ->nullable()
              ->constrained('clubs')
              ->onDelete('cascade')
              ->comment('CLB tổ chức sự kiện');

        // Thông tin cơ bản
        $table->string('name')->comment('Tên sự kiện');
        $table->text('description')->nullable()->comment('Mô tả chi tiết');
        $table->dateTime('start_time')->nullable()->comment('Thời gian bắt đầu');
        $table->dateTime('end_time')->nullable()->comment('Thời gian kết thúc');
        $table->string('location')->comment('Địa điểm tổ chức');

        // Quản lý người tham gia và hiển thị
        $table->integer('max_participants')->nullable()->comment('Giới hạn số người tham gia');
        $table->boolean('is_public')->default(true)->comment('Công khai hoặc nội bộ');

        // Trạng thái & quản trị
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Trạng thái duyệt sự kiện');
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->comment('Người tạo sự kiện');
        $table->foreignId('approval_by')->nullable()->constrained('users')->onDelete('set null')->comment('Người duyệt sự kiện');
        $table->foreignId('media_id')->nullable()->constrained('media')->onDelete('set null')->comment('Hình ảnh / video sự kiện');

        // Ngân sách
        $table->decimal('budget', 10, 2)->nullable()->comment('Ngân sách sự kiện');

        // Thời gian hệ thống
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('events');
    }
}