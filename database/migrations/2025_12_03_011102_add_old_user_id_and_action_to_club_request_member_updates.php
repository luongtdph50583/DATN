<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('club_request_member_updates', function (Blueprint $table) {
            $table->unsignedBigInteger('old_user_id')->nullable()->after('user_id');
            $table->string('action', 20)->nullable()->after('role'); // 'assign' | 'remove'
        });
    }

    public function down()
    {
        Schema::table('club_request_member_updates', function (Blueprint $table) {
            $table->dropColumn(['old_user_id', 'action']);
        });
    }
};