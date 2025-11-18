<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('club_join_form_questions', function (Blueprint $table) {
            $table->foreignId('form_id')->nullable()->after('club_id')->constrained('club_recruitment_forms')->onDelete('cascade')->comment('Form tuyển thành viên');
            $table->index(['form_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_join_form_questions', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->dropIndex(['form_id', 'is_active']);
            $table->dropColumn('form_id');
        });
    }
};
