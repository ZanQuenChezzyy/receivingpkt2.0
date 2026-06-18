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
        Schema::create('purchase_order_issueds', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_order_and_item', 20)->nullable()->index();
            $table->string('material_type', 5)->nullable();
            $table->string('mrp_type', 10);
            $table->string('purchase_order_no', 12)->index();
            $table->mediumInteger('item_no');
            $table->string('material_code', 20)->nullable()->index();
            $table->string('aac', 1)->nullable();
            $table->string('abc_indicator', 1)->nullable();
            $table->text('description');
            $table->decimal('qty_po', 12, 0)->default(0);
            $table->string('uoi', 5);
            $table->string('vendor_id', 20)->nullable()->index();
            $table->string('vendor_name', 100);
            $table->date('date_create')->index();
            $table->date('delivery_date_po')->nullable();
            $table->string('po_status', 2)->nullable()->index();
            $table->string('incoterm', 100)->nullable();
            $table->string('currency', 5)->default('IDR');
            $table->decimal('net_price', 20, 0)->default(0);
            $table->decimal('total_amount_in_lc', 20, 0)->default(0);
            $table->string('requisitioner', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_issueds');
    }
};
