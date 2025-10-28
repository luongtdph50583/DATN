<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubPlansTable extends Migration
{
    public function up()
    {
        Schema::create('club_plans', function (Blueprint $table) {
            $table->id(); // ID kế hoạch
            $table->foreignId('club_id')->constrained()->onDelete('cascade'); // Liên kết với bảng clubs
            $table->string('title'); // Tiêu đề kế hoạch
            $table->text('description'); // Mô tả chi tiết
            $table->date('start_date'); // Ngày bắt đầu
            $table->date('end_date'); // Ngày kết thúc
            $table->decimal('budget', 15, 2)->nullable(); // Ngân sách (15 chữ số, 2 thập phân)
            $table->enum('status', [
                'draft', 'pending', 'approved', 'rejected', 'completed', 'cancelled'
            ])->default('draft'); // Trạng thái
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Người tạo
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Người duyệt
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('club_plans');
    }
}