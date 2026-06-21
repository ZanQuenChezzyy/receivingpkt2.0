<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_chemical_details', function (Blueprint $table) {
            $table->foreignId('chemical_qc_tuv_id')->nullable()->constrained('chemical_qc_tuvs')->nullOnDelete();
        });

        // Migrate data
        $tuvs = DB::table('monitoring_chemical_tuvs')->get();
        foreach ($tuvs as $tuv) {
            DB::table('monitoring_chemical_details')
                ->where('id', $tuv->monitoring_chemical_detail_id)
                ->update(['chemical_qc_tuv_id' => $tuv->chemical_qc_tuv_id]);
        }

        // Drop monitoring_chemical_tuvs
        Schema::dropIfExists('monitoring_chemical_tuvs');
    }

    public function down(): void
    {
        Schema::create('monitoring_chemical_tuvs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_chemical_detail_id')->constrained('monitoring_chemical_details')->cascadeOnDelete();
            $table->foreignId('chemical_qc_tuv_id')->nullable()->constrained('chemical_qc_tuvs')->nullOnDelete();
            $table->decimal('quantity_received', 15, 0)->default(0);
            $table->timestamps();
        });

        $details = DB::table('monitoring_chemical_details')->whereNotNull('chemical_qc_tuv_id')->get();
        foreach ($details as $detail) {
            DB::table('monitoring_chemical_tuvs')->insert([
                'monitoring_chemical_detail_id' => $detail->id,
                'chemical_qc_tuv_id' => $detail->chemical_qc_tuv_id,
                'quantity_received' => $detail->quantity,
            ]);
        }

        Schema::table('monitoring_chemical_details', function (Blueprint $table) {
            $table->dropForeign(['chemical_qc_tuv_id']);
            $table->dropColumn('chemical_qc_tuv_id');
        });
    }
};
