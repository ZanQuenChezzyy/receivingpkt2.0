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
        Schema::create('grs_rdtv_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grs_rdtv_id')->constrained('grs_rdtvs')->cascadeOnDelete();
            $table->foreignId('delivery_order_receipt_id')->nullable()->constrained('delivery_order_receipts')->nullOnDelete();
            $table->string('document_code');
            $table->string('file_path');
            $table->string('status')->default('Matched'); // Matched or Not Found
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grs_rdtv_items');
    }
};
