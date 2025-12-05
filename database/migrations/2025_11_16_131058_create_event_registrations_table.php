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
        Schema::create('event_registrations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained('club_events')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->timestamp('registered_at')->useCurrent();


    $table->enum('attendance_status', ['pending', 'attended', 'absent'])->default('pending');

    $table->unique(['event_id', 'user_id']);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
