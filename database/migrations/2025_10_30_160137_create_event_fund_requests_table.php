<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_fund_requests', function (Blueprint $table) {
            $table->id(); // dùng bigint auto increment thay cho uuid nếu toàn hệ thống dùng bigint
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('requested_by');
            $table->decimal('amount_requested', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            // Khóa ngoại kiểu bigint
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('fund_sources')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_fund_requests');
    }
};
