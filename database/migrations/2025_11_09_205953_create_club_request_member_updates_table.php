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
        Schema::create('club_request_member_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_request_update_id')
                ->constrained('club_request_updates')
                ->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // đổi từ member_id
            $table->enum('role', ['club_manager', 'deputy_manager', 'event_manager', 'communication', 'secretary', 'treasurer', 'member']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_request_member_updates');
    }
};
