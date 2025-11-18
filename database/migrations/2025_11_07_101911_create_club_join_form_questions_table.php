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
        // Schema::create('club_join_form_questions', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('club_id')->constrained()->onDelete('cascade');
        //     $table->string('question'); // Câu hỏi
        //     $table->enum('type', ['text', 'textarea', 'select', 'checkbox'])->default('text'); // Loại câu hỏi
        //     $table->json('options')->nullable(); // Dành cho select / checkbox
        //     $table->integer('order')->default(0); // Thứ tự hiển thị
        //     $table->boolean('is_required')->default(true);
        //     $table->timestamps();
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_join_form_questions');
    }
};
