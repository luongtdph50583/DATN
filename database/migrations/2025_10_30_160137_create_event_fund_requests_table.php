<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_fund_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('requested_by');
            $table->decimal('amount_requested', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->enum('status', ['pending_disbursement','disbursing','disbursed','rejected'])->default('pending_disbursement');
            $table->unsignedBigInteger('approved_by')->nullable();
            
            // Thông tin giải ngân
            $table->unsignedBigInteger('disbursed_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable()->comment('Người từ chối');
            $table->text('disbursement_proof')->nullable();
            $table->timestamp('disbursement_date')->nullable();
            $table->text('rejection_reason')->nullable()->comment('Lý do từ chối giải ngân');

            $table->text('note')->nullable();
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('disbursed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_fund_requests');
    }
};

