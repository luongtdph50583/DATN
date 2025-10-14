<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('registered_at')->useCurrent();
            $table->string('status')->default('pending'); // pending, confirmed, canceled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']); // Đảm bảo một người chỉ đăng ký một lần cho mỗi sự kiện
            $table->index('event_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_registrations');
    }
}