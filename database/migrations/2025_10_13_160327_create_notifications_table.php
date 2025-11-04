<?php

     use Illuminate\Database\Migrations\Migration;
     use Illuminate\Database\Schema\Blueprint;
     use Illuminate\Support\Facades\Schema;

     class CreateNotificationsTable extends Migration
     {
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // 🆕 thêm batch_id để nhóm lượt gửi
            $table->uuid('batch_id')->nullable();

            // 🆕 thêm status để biết trạng thái gửi
            $table->string('status')->default('sent');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }

}
