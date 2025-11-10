<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();

            // Thông tin cơ bản
            $table->string('name')->comment('Tên CLB');
            $table->string('slogan')->nullable()->comment('Khẩu hiệu của CLB');
            $table->text('description')->nullable()->comment('Mô tả chi tiết về CLB');
            $table->string('field')->comment('Lĩnh vực hoạt động');

            $table->enum('status', ['active', 'inactive'])
                ->default('active')
                ->comment('Trạng thái hoạt động của CLB: pending = chờ duyệt, trial = thử nghiệm, active = chính thức, inactive = ngừng hoạt động');

            // Người quản lý hành chính CLB
            $table->foreignId('manager_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Người quản lý hành chính của CLB');

            // Giảng viên đỡ đầu
            $table->foreignId('advisor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Giảng viên đỡ đầu');

            // Liên hệ & giới hạn
            $table->string('email')->nullable()->comment('Email liên hệ');
            $table->string('phone')->nullable()->comment('Số điện thoại liên hệ');
            $table->string('logo')->nullable()->comment('Logo của CLB'); // <-- Thêm cột logo
            $table->integer('member_limit')->nullable()->comment('Giới hạn số lượng thành viên');

            // Kế hoạch & thông tin thêm
            $table->date('founded_at')->nullable()->comment('Ngày thành lập CLB');
            $table->string('location')->nullable()->comment('Địa điểm hoạt động chính');
            $table->text('rules')->nullable()->comment('Nội quy CLB');

            $table->timestamps();
        });

    }


    public function down()
    {
        // Nếu có bảng liên quan
      

        

        Schema::dropIfExists('clubs');
    }

}
