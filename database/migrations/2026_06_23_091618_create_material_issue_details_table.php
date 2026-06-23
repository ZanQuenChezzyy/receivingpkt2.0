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
        Schema::create('material_issue_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_issue_id')->constrained('material_issues')->cascadeOnDelete();
            $table->foreignId('delivery_order_receipt_detail_id')->constrained('delivery_order_receipt_details')->cascadeOnDelete();
            $table->decimal('diminta', 15, 2);
            $table->decimal('diserahkan', 15, 2);
            $table->string('boh')->nullable();
            $table->string('stage_when_issued')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_issue_details');
    }
};
