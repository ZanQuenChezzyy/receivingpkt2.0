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
        Schema::table('material_issues', function (Blueprint $table) {
            $table->string('disetujui_npk')->nullable()->after('disetujui_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_issues', function (Blueprint $table) {
            $table->dropColumn('disetujui_npk');
        });
    }
};
