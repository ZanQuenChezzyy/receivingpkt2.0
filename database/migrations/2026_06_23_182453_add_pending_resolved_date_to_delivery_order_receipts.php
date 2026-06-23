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
            $table->dateTime('pending_resolved_date')->nullable()->after('pending_date')->comment('Tanggal pending dibatalkan/diselesaikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_receipts', function (Blueprint $table) {
            $table->dropColumn('pending_resolved_date');
        });
    }
};
