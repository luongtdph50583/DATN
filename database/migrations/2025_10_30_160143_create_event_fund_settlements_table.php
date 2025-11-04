<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_fund_settlements', function (Blueprint $table) {
            $table->id(); // bigint auto increment (nếu bạn vẫn muốn dùng uuid thì mình có bản dưới)
            $table->decimal('total_spent', 15, 2);
            $table->json('details')->nullable(); // Chi tiết từng khoản chi
            $table->json('receipts')->nullable(); // File hóa đơn, ảnh chứng từ
            $table->decimal('difference', 15, 2)->nullable();
            $table->enum('status', ['pending_review', 'approved', 'needs_revision'])->default('pending_review');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // 🔹 Khóa ngoại
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_fund_settlements');
    }
};
