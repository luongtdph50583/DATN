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
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['post', 'notice', 'document'])->default('post');
            $table->enum('status', ['visible', 'hidden'])->default('visible');
            $table->enum('visibility', ['internal', 'public'])->default('public'); // thêm visibility nếu muốn
            $table->string('thumbnail')->nullable(); // ảnh đại diện
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}