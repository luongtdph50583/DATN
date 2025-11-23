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
    DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'member'");
    DB::statement("ALTER TABLE users ALTER COLUMN status SET DEFAULT 'active'");
}

public function down(): void
{
    DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
    DB::statement("ALTER TABLE users ALTER COLUMN status DROP DEFAULT");
}

};
