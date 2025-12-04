<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('club_request_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->unsignedBigInteger('user_id'); // người đề xuất
            $table->string('name')->nullable();
            $table->string('slogan')->nullable();
            $table->text('description')->nullable();
            $table->string('field')->nullable();
            $table->integer('member_limit')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            // $table->unsignedBigInteger('advisor_id')->nullable();
            // $table->enum('advisor_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo')->nullable();
            $table->text('rules')->nullable();
            $table->string('location')->nullable();
            $table->text('reason')->nullable(); // lý do thay đổi
            $table->text('note')->nullable();   // lý do từ chối
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('advisor_id')->references('id')->on('faculty_members')->onDelete('set null');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_request_updates');
    }
};
