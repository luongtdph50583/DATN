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
        Schema::create('club_recruitment_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->onDelete('cascade')->comment('CLB');
            $table->string('name')->comment('Tên form');
            $table->text('description')->nullable()->comment('Mô tả form');
            $table->boolean('is_active')->default(true)->comment('Form có đang hoạt động không');
            $table->boolean('is_default')->default(false)->comment('Form mặc định của CLB');
            $table->integer('order')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['club_id', 'is_active']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_recruitment_forms');
    }
};
