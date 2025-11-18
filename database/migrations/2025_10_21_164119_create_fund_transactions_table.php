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

            // Trạng thái giao dịch
            $table->enum('status', ['pending', 'approved', 'in_progress', 'completed'])
                  ->default('pending')
                  ->comment('Trạng thái: chờ duyệt, duyệt, đang thu/chi, đã thu/chi');

            // Khoảng thời gian thu/chi (dành cho income)
            $table->date('start_date')->nullable()->comment('Ngày bắt đầu thu/chi');
            $table->date('end_date')->nullable()->comment('Ngày kết thúc thu/chi');

            // Người tạo và người duyệt
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->comment('Người tạo giao dịch');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->comment('Người duyệt giao dịch');

            // Tài liệu minh chứng
            $table->string('receipt')->nullable()->comment('Hình ảnh/Chứng từ');
            $table->string('excel_file')->nullable()->comment('File Excel kiểm tra thu (nếu có)');

            $table->timestamps(); // created_at & updated_at
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
