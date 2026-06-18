<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'material_category',
    'purchase_order_issued_id',
    'qc_by',
    'do_number',
    'quantity',
    'tahapan',
    'received_by',
    'received_date',
    'location_id',
    'is_qty_tolerance',
    'has_update_progress',
    'notes',
    'tanggal_pengajuan_simala',
    'tanggal_pengambilan_sample',
    'tanggal_terbit_coa',
    'leadtime_coa',
    'doc_status',
    'created_by',
])]
class MonitoringChemical extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'is_qty_tolerance' => 'boolean',
            'has_update_progress' => 'boolean',
            'tanggal_pengajuan_simala' => 'date',
            'tanggal_pengambilan_sample' => 'date',
            'tanggal_terbit_coa' => 'date',
            'leadtime_coa' => 'integer',
        ];
    }

    public function purchaseOrderIssued(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderIssued::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationReceiving::class, 'location_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function monitoringChemicalDetails(): HasMany
    {
        return $this->hasMany(MonitoringChemicalDetail::class);
    }
}
