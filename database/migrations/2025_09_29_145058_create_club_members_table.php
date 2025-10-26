<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubMembersTable extends Migration
{
    public function up()
    {
Schema::create('club_members', function (Blueprint $table) {
        $table->id(); // Khóa chính

        $table->foreignId('club_id')->constrained()->onDelete('cascade')->comment('Liên kết CLB');
        $table->foreignId('member_id')->constrained()->onDelete('cascade')->comment('Liên kết thành viên');

        $table->enum('role', ['admin', 'member'])->default('member')->comment('Vai trò trong CLB');
        $table->timestamp('joined_at')->useCurrent()->comment('Ngày tham gia');
        
        $table->timestamps(); // Thời gian tạo/cập nhật
    });
    }

    public function down()
    {
        Schema::dropIfExists('club_members');
    }
}
