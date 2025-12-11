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
        Schema::create('club_member_violations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('club_id')
                ->constrained('clubs')
                ->onDelete('cascade');

            $table->foreignId('member_id')
                ->constrained('club_members')
                ->onDelete('cascade');

            $table->foreignId('reported_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('người báo cáo vi phạm');

            $table->string('type')->comment('Loại vi phạm: warning, strike, serious, ...');
            $table->text('description')->nullable()->comment('Mô tả vi phạm');
            $table->timestamp('issued_at')->nullable()->comment('Ngày vi phạm');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club_member_violations');
    }
};
