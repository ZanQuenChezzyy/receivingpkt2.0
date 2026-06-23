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
            $table->longText('diminta_signature')->nullable()->after('npk');
            $table->longText('disetujui_signature')->nullable()->after('disetujui_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_issues', function (Blueprint $table) {
            $table->dropColumn(['diminta_signature', 'disetujui_signature']);
        });
    }
};
