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
        Schema::create('club_update_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('club_id')
                ->constrained('clubs')
                ->onDelete('cascade')
                ->comment('CLB được thay đổi');

            $table->foreignId('admin_id') // thêm cột admin_id
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Admin thực hiện thay đổi');

            $table->foreignId('proposer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Người đề xuất thay đổi (chủ nhiệm CLB)');

            $table->json('changed_fields')
                ->nullable()
                ->comment('Các trường được thay đổi, lưu giá trị cũ và mới');

            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_update_logs');
    }
};
