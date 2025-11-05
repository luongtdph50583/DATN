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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');

            $table->foreignId('clb_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');

            // Thay enum bằng JSON để lưu nhiều quyền
        $table->json('access_level')->nullable()->comment('Quyền truy cập, có thể chọn nhiều');

            $table->string('tags')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();

            // Nếu không cần ẩn/hiện thì bỏ
            // $table->boolean('is_visible')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });



    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
