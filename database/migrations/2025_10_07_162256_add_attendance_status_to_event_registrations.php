<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendanceStatusToEventRegistrations extends Migration
{
    public function up()
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->enum('attendance_status', ['pending', 'attended', 'absent'])->default('pending')->after('registered_at');
        });
    }

    public function down()
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn('attendance_status');
        });
    }
}