<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Ngân sách dự kiến (ước tính khi lập kế hoạch)
            $table->decimal('budget_estimated', 15, 2)->default(0)->after('media_id');

            // Ngân sách hiện có (thực tế còn lại trong quỹ sự kiện)
            $table->decimal('budget_current', 15, 2)->default(0)->after('budget_estimated');

            // Ngân sách thực dụng (tổng chi tiêu sau khi kết thúc sự kiện)
            $table->decimal('budget_used', 15, 2)->default(0)->after('budget_current');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['budget_estimated', 'budget_current', 'budget_used']);
        });
    }
};

