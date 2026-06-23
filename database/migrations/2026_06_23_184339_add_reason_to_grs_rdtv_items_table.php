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
        Schema::table('grs_rdtv_items', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('status')->comment('Alasan ditolak untuk RDTV');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grs_rdtv_items', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
