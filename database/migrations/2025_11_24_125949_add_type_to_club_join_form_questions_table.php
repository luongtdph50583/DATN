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
            // Thêm cột type kiểu enum
            $table->enum('type', [
                'short_text',
                'long_text',
                'number',
                'select',
                'checkbox',
                'radio',
                'email',
                'phone',
                'date',
                'textarea'
            ])->default('short_text')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('club_join_form_questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
