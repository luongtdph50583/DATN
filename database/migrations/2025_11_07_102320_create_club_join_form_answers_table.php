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
        Schema::create('club_join_form_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('club_join_requests')->onDelete('cascade')->comment('Yêu cầu tham gia CLB');
            $table->foreignId('question_id')->constrained('club_join_form_questions')->onDelete('cascade')->comment('Câu hỏi');
            $table->text('answer')->nullable()->comment('Câu trả lời');
            $table->json('answer_json')->nullable()->comment('Câu trả lời dạng JSON (cho checkbox, select multiple)');
            $table->timestamps();

            // Đảm bảo mỗi request chỉ trả lời 1 lần cho mỗi câu hỏi
            $table->unique(['request_id', 'question_id']);
            
            // Index để tìm kiếm nhanh
            $table->index('request_id');
            $table->index('question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_join_form_answers');
    }
};
