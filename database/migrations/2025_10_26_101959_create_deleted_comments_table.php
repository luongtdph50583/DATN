<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deleted_comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('comment_id')->comment('ID comment bị xóa');
            $table->unsignedBigInteger('deleted_by')->comment('ID admin hoặc người xóa');

            $table->text('deleted_reason')->comment('Lý do xóa comment');

            $table->timestamps(); // created_at = thời gian xóa, updated_at không dùng nhiều
            $table->softDeletes(); // nếu muốn cho phép khôi phục record xóa lý do

            // Khóa ngoại (nếu muốn)
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_comments');
    }
};
