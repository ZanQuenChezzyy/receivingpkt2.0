<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename existing monitoring_chemical_details to monitoring_chemical_tuvs
        Schema::rename('monitoring_chemical_details', 'monitoring_chemical_tuvs');

        // 2. Drop the foreign key to monitoring_chemicals
        Schema::table('monitoring_chemical_tuvs', function (Blueprint $table) {
            $table->dropForeign('monitoring_chemical_details_monitoring_chemical_id_foreign');
        });

        // 3. Create the new monitoring_chemical_details table
        Schema::create('monitoring_chemical_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_chemical_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_issued_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 15, 0);
            $table->string('tahapan')->nullable();
            $table->boolean('is_qty_tolerance')->default(false);
            $table->boolean('has_update_progress')->default(false);
            $table->date('tanggal_pengajuan_simala')->nullable();
            $table->date('tanggal_pengambilan_sample')->nullable();
            $table->date('tanggal_terbit_coa')->nullable();
            $table->integer('leadtime_coa')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('location_receivings')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Migrate existing data from monitoring_chemicals to monitoring_chemical_details
        $existing = DB::table('monitoring_chemicals')->get();
        foreach ($existing as $row) {
            if ($row->purchase_order_issued_id) {
                $detailId = DB::table('monitoring_chemical_details')->insertGetId([
                    'monitoring_chemical_id' => $row->id,
                    'purchase_order_issued_id' => $row->purchase_order_issued_id,
                    'quantity' => $row->quantity ?? 0,
                    'tahapan' => $row->tahapan,
                    'is_qty_tolerance' => $row->is_qty_tolerance ?? false,
                    'has_update_progress' => $row->has_update_progress ?? false,
                    'tanggal_pengajuan_simala' => $row->tanggal_pengajuan_simala,
                    'tanggal_pengambilan_sample' => $row->tanggal_pengambilan_sample,
                    'tanggal_terbit_coa' => $row->tanggal_terbit_coa,
                    'leadtime_coa' => $row->leadtime_coa,
                    'notes' => $row->notes,
                    'location_id' => $row->location_id,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);

                // 5. Update monitoring_chemical_tuvs to point to the new detail ID
                DB::table('monitoring_chemical_tuvs')
                    ->where('monitoring_chemical_id', $row->id)
                    ->update(['monitoring_chemical_id' => $detailId]);
            }
        }

        // 6. Rename monitoring_chemical_id to monitoring_chemical_detail_id in monitoring_chemical_tuvs
        Schema::table('monitoring_chemical_tuvs', function (Blueprint $table) {
            $table->renameColumn('monitoring_chemical_id', 'monitoring_chemical_detail_id');
        });

        Schema::table('monitoring_chemical_tuvs', function (Blueprint $table) {
            $table->foreign('monitoring_chemical_detail_id', 'fk_mon_chem_tuvs_detail_id')->references('id')->on('monitoring_chemical_details')->cascadeOnDelete();
        });

        // 7. Drop the migrated columns from monitoring_chemicals
        Schema::table('monitoring_chemicals', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_issued_id']);
            $table->dropForeign(['location_id']);

            $table->dropColumn([
                'purchase_order_issued_id',
                'quantity',
                'tahapan',
                'is_qty_tolerance',
                'has_update_progress',
                'tanggal_pengajuan_simala',
                'tanggal_pengambilan_sample',
                'tanggal_terbit_coa',
                'leadtime_coa',
                'notes',
                'location_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the columns to monitoring_chemicals
        Schema::table('monitoring_chemicals', function (Blueprint $table) {
            $table->foreignId('purchase_order_issued_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 15, 0)->nullable();
            $table->string('tahapan')->nullable();
            $table->boolean('is_qty_tolerance')->default(false);
            $table->boolean('has_update_progress')->default(false);
            $table->date('tanggal_pengajuan_simala')->nullable();
            $table->date('tanggal_pengambilan_sample')->nullable();
            $table->date('tanggal_terbit_coa')->nullable();
            $table->integer('leadtime_coa')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('location_receivings')->nullOnDelete();
        });

        // We won't reverse the data migration completely in down(), just structural revert to prevent data loss.
        // Or if we must, we take the first item per monitoring_chemical and push back to header.
        $details = DB::table('monitoring_chemical_details')->get();
        foreach ($details as $detail) {
            DB::table('monitoring_chemicals')->where('id', $detail->monitoring_chemical_id)->update([
                'purchase_order_issued_id' => $detail->purchase_order_issued_id,
                'quantity' => $detail->quantity,
                'tahapan' => $detail->tahapan,
                'is_qty_tolerance' => $detail->is_qty_tolerance,
                'has_update_progress' => $detail->has_update_progress,
                'tanggal_pengajuan_simala' => $detail->tanggal_pengajuan_simala,
                'tanggal_pengambilan_sample' => $detail->tanggal_pengambilan_sample,
                'tanggal_terbit_coa' => $detail->tanggal_terbit_coa,
                'leadtime_coa' => $detail->leadtime_coa,
                'notes' => $detail->notes,
                'location_id' => $detail->location_id,
            ]);

            DB::table('monitoring_chemical_tuvs')
                ->where('monitoring_chemical_detail_id', $detail->id)
                ->update(['monitoring_chemical_detail_id' => $detail->monitoring_chemical_id]);
        }

        Schema::table('monitoring_chemical_tuvs', function (Blueprint $table) {
            $table->dropForeign('fk_mon_chem_tuvs_detail_id');
            $table->renameColumn('monitoring_chemical_detail_id', 'monitoring_chemical_id');
        });

        Schema::table('monitoring_chemical_tuvs', function (Blueprint $table) {
            $table->foreign('monitoring_chemical_id')->references('id')->on('monitoring_chemicals')->cascadeOnDelete();
        });

        Schema::dropIfExists('monitoring_chemical_details');
        Schema::rename('monitoring_chemical_tuvs', 'monitoring_chemical_details');
    }
};
