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
        // Schema::create('faculty_members', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')
        //         ->constrained('users')
        //         ->onDelete('cascade')
        //         ->comment('Liên kết tài khoản user');
        //     $table->string('employee_code')->nullable()->comment('Mã giảng viên / nhân viên');
        //     $table->string('department')->nullable()->comment('Khoa / Bộ môn');
        //     $table->string('title')->nullable()->comment('Chức danh: Giảng viên, Trợ giảng...');
        //     $table->string('position')->nullable()->comment('Chức vụ: Trưởng bộ môn, Phó khoa...');
        //     $table->string('office_phone')->nullable()->comment('Số điện thoại cơ quan');
        //     $table->string('phone_personal')->nullable()->comment('Số điện thoại cá nhân');
        //     $table->string('email_official')->nullable()->comment('Email cơ quan');
        //     $table->string('office_location')->nullable()->comment('Văn phòng / Phòng làm việc');
        //     $table->boolean('verified')->default(false)->comment('Đã xác thực là giảng viên');
        //     $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái công tác');
        //     $table->date('start_date')->nullable()->comment('Ngày bắt đầu công tác tại trường');
        //     $table->timestamps();
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_members');
    }
};
