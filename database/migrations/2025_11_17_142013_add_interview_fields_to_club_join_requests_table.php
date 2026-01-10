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
        Schema::table('club_join_requests', function (Blueprint $table) {
            // Thông tin phỏng vấn
            $table->foreignId('interviewer_id')->nullable()->after('interview_scheduled_at')->constrained('users')->onDelete('set null')->comment('Người phỏng vấn');
            $table->string('interview_location')->nullable()->after('interviewer_id')->comment('Địa điểm phỏng vấn');
            $table->text('interview_note')->nullable()->after('interview_location')->comment('Ghi chú phỏng vấn');
            
            // Kết quả phỏng vấn và điểm danh
            $table->enum('interview_result', ['pending', 'completed', 'no_show', 'cancelled'])->nullable()->after('interview_note')->default('pending')->comment('Kết quả phỏng vấn');
            $table->integer('interview_score')->nullable()->after('interview_result')->comment('Điểm phỏng vấn (nếu có)');
            $table->timestamp('interview_completed_at')->nullable()->after('interview_score')->comment('Thời gian hoàn thành phỏng vấn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_join_requests', function (Blueprint $table) {
            $table->dropForeign(['interviewer_id']);
            $table->dropColumn([
                'interviewer_id',
                'interview_location',
                'interview_note',
                'interview_result',
                'interview_completed_at'
            ]);
        });
    }
};
