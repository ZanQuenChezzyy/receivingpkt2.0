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
            $table->string('qr_103_code', 100)->nullable()->after('post_103');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_receipts', function (Blueprint $table) {
            $table->dropColumn('qr_103_code');
        });
    }
};
