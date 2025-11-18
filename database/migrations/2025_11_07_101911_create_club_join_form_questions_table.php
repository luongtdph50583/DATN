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
        Schema::create('club_join_form_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->onDelete('cascade')->comment('CLB');
            $table->string('question')->comment('Câu hỏi');
            $table->text('description')->nullable()->comment('Mô tả thêm cho câu hỏi');
            $table->enum('type', ['text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'email', 'phone', 'date'])->default('text')->comment('Loại câu hỏi');
            $table->json('options')->nullable()->comment('Các lựa chọn (dành cho select, checkbox, radio)');
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->boolean('is_required')->default(true)->comment('Bắt buộc trả lời');
            $table->boolean('is_active')->default(true)->comment('Có đang sử dụng không');
            $table->string('validation_rules')->nullable()->comment('Quy tắc validation (ví dụ: min:5,max:100)');
            $table->timestamps();
            $table->softDeletes();

            // Index để tìm kiếm nhanh
            $table->index(['club_id', 'is_active']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_join_form_questions');
    }
};
