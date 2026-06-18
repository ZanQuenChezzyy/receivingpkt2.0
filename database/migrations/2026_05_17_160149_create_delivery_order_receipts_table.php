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
        Schema::create('delivery_order_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_npk_id')->nullable()->unique()->constrained('monitoring_npks')->cascadeOnDelete();
            $table->foreignId('monitoring_chemical_id')->nullable()->unique()->constrained('monitoring_chemicals')->cascadeOnDelete();
            $table->string('delivery_oder_no', 25)->comment('Bisa diisi nomor DOF/Memo jika DO asli belum ada');
            $table->date('received_date')->comment('Tanggal terima di sistem (Sesuai surat DOF / AWB)');
            $table->foreignId('received_by')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->string('source_type', 20)->nullable();
            $table->string('stage', 100)->nullable();
            $table->string('document_code', 100)->nullable()->unique();
            $table->string('status', 20)->default('Diterima');
            $table->dateTime('post_103')->nullable();
            $table->string('delay_reason')->nullable()->comment('Alasan jika post 103 tertunda > 24 jam');
            $table->text('delay_notes')->nullable()->comment('Catatan tambahan untuk penundaan post 103');
            $table->string('document_path')->nullable()->comment('Path file upload DO atau dokumen lainnya');
            $table->string('receipt_mode', 30)->default('Standard')->comment('Standard, Termin, DOF_Incoterm');
            $table->string('dof_number', 100)->nullable()->comment('Nomor Surat DOF, cth: 21658/D/PL/...');
            $table->date('dof_date')->nullable()->comment('Tanggal Surat DOF');
            $table->boolean('is_physically_received')->default(false)->comment('True jika barang sudah benar-benar tiba di gudang Bontang');
            $table->date('physical_received_date')->nullable()->comment('Tanggal barang fisik tiba');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_receipts');
    }
};
