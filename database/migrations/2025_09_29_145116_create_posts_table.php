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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Liên kết với CLB và người đăng
            $table->foreignId('club_id')
                  ->constrained('clubs') // tham chiếu trực tiếp bảng 'clubs'
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained() // mặc định tham chiếu 'users.id'
                  ->onDelete('cascade');

            // Nội dung bài viết
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['post', 'notice', 'document'])->default('post');

            // Trạng thái hiển thị và kiểm duyệt
            $table->boolean('is_visible')->default(true); // ẩn/hiện
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('visibility', ['internal', 'public'])->default('public');

            // Metadata
            $table->string('thumbnail')->nullable();
            $table->boolean('is_featured')->default(false);

            // Kiểm duyệt
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete(); // admin duyệt
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable(); // ngày xuất bản
            $table->text('rejection_reason')->nullable();

            // Soft delete và timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
