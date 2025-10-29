<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
   public function up()
{
    Schema::create('comments', function (Blueprint $table) {
        $table->id(); // Khóa chính

        // Liên kết bài viết và người dùng
        $table->foreignId('post_id')->constrained('posts')->onDelete('cascade')->comment('Bài viết được bình luận');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Người bình luận');

        // Bình luận cha (cho phép trả lời bình luận)
        $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade')->comment('Bình luận cha');

        // Nội dung và trạng thái
        $table->text('content')->comment('Nội dung bình luận');
        $table->enum('status', ['visible', 'hidden', 'pending'])->default('visible')->comment('Trạng thái hiển thị');
        $table->integer('likes_count')->default(0)->comment('Số lượt thích');

        $table->timestamps(); // Thời gian tạo và cập nhật
    });
}


    public function down()
    {
        Schema::dropIfExists('comments');
    }
}