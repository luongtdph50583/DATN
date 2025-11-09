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
        // Schema::create('club_interview_schedules', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('request_id')->constrained('club_join_requests')->onDelete('cascade');
        //     $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('set null');
        //     $table->dateTime('scheduled_at');
        //     $table->string('location')->nullable();
        //     $table->enum('status', ['scheduled', 'completed', 'canceled'])->default('scheduled');
        //     $table->text('note')->nullable();
        //     $table->timestamps();
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_interview_schedules');
    }
};
