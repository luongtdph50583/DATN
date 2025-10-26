<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
      public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Khóa chính
            $table->string('name')->comment('Họ tên');
            $table->string('email')->unique()->comment('Email đăng nhập');
            $table->string('password')->comment('Mật khẩu');
            $table->enum('role', ['admin', 'club_manager', 'member'])->default('member')->comment('Vai trò');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái');
            $table->string('avatar')->nullable()->comment('Ảnh đại diện');
            $table->rememberToken()->comment('Token ghi nhớ đăng nhập');
            $table->timestamps(); // Thời gian tạo/cập nhật
        });
    }
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
