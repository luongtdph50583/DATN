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
    Schema::create('fund_transactions', function (Blueprint $table) {
        $table->id(); // Khóa chính

        // Liên kết CLB và sự kiện
        $table->foreignId('club_id')->constrained()->onDelete('cascade')->comment('CLB liên quan');
        $table->foreignId('event_id')->nullable()->constrained()->onDelete('set null')->comment('Sự kiện liên quan (nếu có)');

        // Thông tin giao dịch
        $table->enum('type', ['income', 'expense'])->comment('Loại giao dịch: thu hoặc chi');
        $table->decimal('amount', 15, 2)->comment('Số tiền giao dịch');
        $table->text('description')->comment('Mô tả chi tiết');
        $table->string('category')->nullable()->comment('Danh mục giao dịch');

        // Trạng thái duyệt
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('Trạng thái duyệt');

        // Người tạo và người duyệt
        $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->comment('Người tạo giao dịch');
        $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Người duyệt giao dịch');

        // Tài liệu minh chứng
        $table->string('receipt')->nullable()->comment('Hóa đơn hoặc chứng từ');

        $table->timestamps(); // Thời gian tạo và cập nhật
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_transactions');
    }
};
