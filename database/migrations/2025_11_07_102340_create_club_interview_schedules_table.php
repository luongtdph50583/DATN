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
        Schema::create('club_interview_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('club_join_requests')->onDelete('cascade')->comment('Yêu cầu tham gia CLB');
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('set null')->comment('Người phỏng vấn');
            $table->foreignId('club_id')->constrained()->onDelete('cascade')->comment('CLB');
            $table->dateTime('scheduled_at')->comment('Thời gian phỏng vấn');
            $table->string('location')->nullable()->comment('Địa điểm phỏng vấn');
            $table->enum('status', ['scheduled', 'completed', 'no_show', 'cancelled'])->default('scheduled')->comment('Trạng thái phỏng vấn');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->text('interview_result')->nullable()->comment('Kết quả phỏng vấn');
            $table->integer('score')->nullable()->comment('Điểm phỏng vấn');
            $table->timestamp('completed_at')->nullable()->comment('Thời gian hoàn thành');
            $table->timestamps();

            // Index để tìm kiếm nhanh
            $table->index(['club_id', 'status']);
            $table->index(['scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_interview_schedules');
    }
};
