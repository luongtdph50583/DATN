<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
    $table->id(); // Khóa chính
    $table->string('name')->comment('Tên CLB');
    $table->text('description')->nullable()->comment('Mô tả');
    $table->string('logo')->nullable()->comment('Logo CLB');
    $table->string('field')->comment('Lĩnh vực hoạt động');
    $table->enum('status', ['active', 'pending', 'inactive'])->default('pending')->comment('Trạng thái');
    $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null')->comment('Quản lý CLB');

    // Liên hệ & giới hạn thành viên
    $table->string('email')->nullable()->comment('Email liên hệ');
    $table->string('phone')->nullable()->comment('SĐT liên hệ');
    $table->integer('member_limit')->nullable()->comment('Giới hạn thành viên');

    $table->timestamps(); // Thời gian tạo/cập nhật
});

    }

    public function down()
    {
        Schema::dropIfExists('clubs');
    }
}
