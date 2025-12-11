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
        Schema::create('club_request_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_request_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('status')->default(false); // true = đã xác nhận
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->foreign('club_request_id')->references('id')->on('club_requests')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_request_confirmations');
    }
};
