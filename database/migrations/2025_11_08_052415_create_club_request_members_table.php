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
        Schema::create('club_request_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_request_id')->constrained('club_requests')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('role', [
                'deputy_manager',
                'secretary',
                'treasurer',
                'event_manager',
                'communication',
                'member'
            ])->default('member')->comment('Vai trò trong CLB');

            // Thêm cột type để phân biệt loại đề xuất

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_request_members');
    }
};
