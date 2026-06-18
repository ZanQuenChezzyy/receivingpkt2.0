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
        Schema::create('qc_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_receipt_id')->constrained('delivery_order_receipts')->cascadeOnDelete();
            $table->string('status', 50)->comment('Kirim, Revisi, Kembali');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_histories');
    }
};
