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
        Schema::create('material_issues', function (Blueprint $table) {
            $table->id();
            $table->string('mir_number')->unique();
            $table->date('tanggal');
            $table->foreignId('purchase_order_issued_id')->constrained('purchase_order_issueds')->cascadeOnDelete();
            $table->string('no_hp');
            $table->string('no_reservasi')->nullable();
            $table->string('departemen');
            $table->string('bagian');
            $table->string('no_jor_wo')->nullable();
            $table->text('digunakan_untuk');
            $table->string('no_alat')->nullable();
            $table->string('kode_biaya')->nullable();
            $table->string('diminta_oleh')->nullable();
            $table->string('disetujui_oleh')->nullable();
            $table->string('diketahui_oleh')->nullable();
            $table->string('diserahkan_oleh')->nullable();
            $table->string('diterima_oleh')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_issues');
    }
};
