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

            // Liên kết CLB
            $table->foreignId('club_id')
                ->constrained()
                ->onDelete('cascade')
                ->comment('Liên kết đến CLB');

            // Liên kết thành viên (user)
            $table->foreignId('member_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Liên kết đến thành viên');

            // Vai trò trong CLB
            $table->enum('role', [
                'club_manager',        // Chủ nhiệm
                'deputy_manager',      // Phó chủ nhiệm
                'secretary',           // Thư ký
                'treasurer',           // Thủ quỹ
                'event_manager',       // Quản lý sự kiện
                'communication',       // Truyền thông
                'member',              // Thành viên thường
            ])->default('member')->comment('Vai trò trong CLB');

            // Trạng thái & ghi chú
            $table->enum('status', ['active', 'inactive', 'left'])
                ->default('active')
                ->comment('Trạng thái thành viên trong CLB');
            $table->text('note')->nullable()->comment('Ghi chú: thành tích, cảnh cáo, v.v.');

            $table->timestamp('joined_at')->useCurrent()->comment('Ngày tham gia');
            $table->timestamp('left_at')->nullable()->comment('Ngày rời CLB'); // thêm trường mới

            $table->timestamps(); // created_at, updated_at

            // Tránh trùng lặp thành viên trong cùng CLB
            $table->unique(['club_id', 'member_id']);
            $table->timestamp('appointed_at')->nullable()->comment('Ngày bổ nhiệm làm ban quản lý');
        });
    }


    public function down()
    {
        Schema::dropIfExists('club_members');
    }
}
