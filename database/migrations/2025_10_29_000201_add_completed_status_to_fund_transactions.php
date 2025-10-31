<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify enum to add 'completed'
        Schema::table('fund_transactions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])
                ->default('pending')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('fund_transactions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->change();
        });
    }
};



