<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    public function up()
{
    Schema::create('members', function (Blueprint $table) {
        $table->id(); // Khóa chính
        $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Liên kết người dùng');

        // Thông tin cá nhân
        $table->string('student_code')->nullable()->comment('Mã số sinh viên'); // <-- thêm cột này
        $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('Giới tính');
        $table->date('date_of_birth')->nullable()->comment('Ngày sinh');
        $table->string('address')->nullable()->comment('Địa chỉ');
        $table->string('course')->nullable()->comment('Khóa học');
        $table->string('major')->nullable()->comment('Chuyên ngành');

        // Thông tin định danh
        $table->string('citizen_id')->nullable()->comment('Số CCCD');
        $table->date('issued_date')->nullable()->comment('Ngày cấp');
        $table->string('issued_place')->nullable()->comment('Nơi cấp');
        $table->string('ethnicity')->nullable()->comment('Dân tộc');
        $table->string('phone')->nullable()->comment('Số điện thoại');

        // Trạng thái thành viên
        $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái');

        $table->timestamps(); // Thời gian tạo/cập nhật
    });



    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
}
