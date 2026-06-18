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
        Schema::create('transmittal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transmittal_id')->constrained('transmittals')->cascadeOnDelete();
            $table->foreignId('delivery_order_receipt_id')->constrained('delivery_order_receipts')->cascadeOnDelete();
            $table->string('status', 20)->default('Kirim');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transmittal_items');
    }
};
