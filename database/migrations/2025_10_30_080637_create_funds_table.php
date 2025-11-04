<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funds', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại liên kết tới bảng clubs
            $table->foreignId('club_id')
                  ->constrained('clubs')
                  ->onDelete('cascade');

            // Số tiền ban đầu (mặc định = 0)
            $table->decimal('initial_balance', 15, 2)->default(0);

            // Tổng số tiền hiện tại (cập nhật sau mỗi giao dịch)
            $table->decimal('balance', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funds');
    }
};
