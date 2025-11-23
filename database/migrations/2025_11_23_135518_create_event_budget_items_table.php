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
        Schema::create('event_budget_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained()->onDelete('cascade');
    $table->string('item_name');                    // Ví dụ: Thuê loa đài
    $table->text('description')->nullable();        // Mô tả chi tiết
    $table->decimal('estimated_cost', 15, 2);       // Dự kiến
    $table->decimal('actual_cost', 15, 2)->nullable(); // Thực tế (sau này)
    $table->enum('type', ['club_fund', 'school_fund', 'other'])->default('club_fund');
    $table->integer('order')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_budget_items');
    }
};
