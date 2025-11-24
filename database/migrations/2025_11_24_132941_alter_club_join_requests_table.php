<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('club_join_requests', function (Blueprint $table) {
        $table->enum('status', [
            'pending',
            'scheduling_interview',
            'interview',
            'interview_completed',
            'approved',
            'rejected',
            'cancelled'
        ])->default('pending')->change();
    });
}

public function down()
{
    // Nếu muốn rollback về ENUM cũ
}

};
