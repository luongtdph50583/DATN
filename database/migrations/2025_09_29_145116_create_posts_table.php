<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Liên kết với CLB và người đăng
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Nội dung bài viết
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['post', 'notice', 'document'])->default('post'); // loại bài viết

            // Trạng thái hiển thị và kiểm duyệt
            $table->boolean('is_visible')->default(true); // ẩn/hiện
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // trạng thái duyệt
            $table->enum('visibility', ['internal', 'public'])->default('public'); // ai được xem

            // Metadata
            $table->string('thumbnail')->nullable(); // ảnh đại diện
            $table->boolean('is_featured')->default(false); // bài viết nổi bật

            // Kiểm duyệt
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // admin duyệt
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Thống kê


            // Soft delete và timestamps
            $table->softDeletes();
            $table->timestamps();
        });

    }
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
Schema::create('posts', function (Blueprint $table) {
    $table->id();

    // Liên kết với CLB và người đăng
    $table->foreignId('club_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');

    // Nội dung bài viết
    $table->string('title');
    $table->text('content');
    $table->enum('type', ['post', 'notice', 'document'])->default('post'); // loại bài viết

    // Trạng thái hiển thị và kiểm duyệt
    $table->boolean('is_visible')->default(true); // ẩn/hiện
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // trạng thái duyệt
    $table->enum('visibility', ['internal', 'public'])->default('public'); // ai được xem

    // Metadata
    $table->string('thumbnail')->nullable(); // ảnh đại diện
    $table->boolean('is_featured')->default(false); // bài viết nổi bật

    // Kiểm duyệt
    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // admin duyệt
    $table->timestamp('approved_at')->nullable();
    $table->timestamp('published_at')->nullable(); // <-- thêm vào đây
    $table->text('rejection_reason')->nullable();

    // Soft delete và timestamps
    $table->softDeletes();
    $table->timestamps();
});
