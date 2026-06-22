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
        Schema::table('delivery_order_receipts', function (Blueprint $table) {
            $table->integer('arrival_sequence')->nullable()->after('is_physically_received');
            $table->string('incoterms', 100)->nullable()->after('arrival_sequence');
            $table->string('current_location', 255)->nullable()->after('incoterms');
            $table->date('eta_date')->nullable()->after('current_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_receipts', function (Blueprint $table) {
            $table->dropColumn([
                'arrival_sequence',
                'incoterms',
                'current_location',
                'eta_date',
            ]);
        });
    }
};
