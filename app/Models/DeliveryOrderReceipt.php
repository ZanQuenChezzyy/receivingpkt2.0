<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'monitoring_npk_id',
    'monitoring_chemical_id',
    'delivery_oder_no',
    'received_date',
    'received_by',
    'created_by',
    'source_type',
    'stage',
    'document_code',
    'status',
    'post_103',
    'qr_103_code',
    'receipt_mode',
    'dof_number',
    'dof_date',
    'is_physically_received',
    'physical_received_date',
])]
class DeliveryOrderReceipt extends Model
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
            'post_103' => 'datetime',
            'dof_date' => 'date',
            'is_physically_received' => 'boolean',
            'physical_received_date' => 'date',
        ];
    }

    public function transmittalItems(): HasMany
    {
        return $this->hasMany(TransmittalItem::class);
    }

    public function transmittals(): BelongsToMany
    {
        return $this->belongsToMany(Transmittal::class, 'transmittal_items');
    }

    public function qcHistories(): HasMany
    {
        return $this->hasMany(QcHistory::class);
    }

    public function monitoringNpk(): BelongsTo
    {
        return $this->belongsTo(MonitoringNpk::class);
    }

    public function monitoringChemical(): BelongsTo
    {
        return $this->belongsTo(MonitoringChemical::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deliveryOrderReceiptDetails(): HasMany
    {
        return $this->hasMany(DeliveryOrderReceiptDetail::class);
    }
}
