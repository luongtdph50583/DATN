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
        // Schema::create('club_join_form_answers', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('request_id')->constrained('club_join_requests')->onDelete('cascade');
        //     $table->foreignId('question_id')->constrained('club_join_form_questions')->onDelete('cascade');
        //     $table->text('answer')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_join_form_answers');
    }
};
