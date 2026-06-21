<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringChemicalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitoring_chemical_id',
        'purchase_order_issued_id',
        'chemical_qc_tuv_id',
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
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'is_qty_tolerance' => 'boolean',
            'has_update_progress' => 'boolean',
            'tanggal_pengajuan_simala' => 'date',
            'tanggal_pengambilan_sample' => 'date',
            'tanggal_terbit_coa' => 'date',
            'leadtime_coa' => 'integer',
        ];
    }

    public function monitoringChemical(): BelongsTo
    {
        return $this->belongsTo(MonitoringChemical::class);
    }

    public function purchaseOrderIssued(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderIssued::class);
    }

    public function locationReceiving(): BelongsTo
    {
        return $this->belongsTo(LocationReceiving::class, 'location_id');
    }

    public function chemicalQcTuv(): BelongsTo
    {
        return $this->belongsTo(ChemicalQcTuv::class);
    }
}
