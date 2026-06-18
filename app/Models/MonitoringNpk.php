<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'purchase_order_terbit_id',
    'delivery_oder_number',
    'location_id',
    'sample_receivied_date',
    'stage',
    'delivery_oder_delivery_date',
    'purchase_order_103_date',
    'received_date',
    'purchase_order_status',
    'purchase_order_status_a_date',
    'purchase_order_status_b_date',
    'purchase_order_status_a_files',
    'laprima_date',
    'coa_date',
    'coa_files',
    'doc_status',
    'created_by',
])]
class MonitoringNpk extends Model
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
            'sample_receivied_date' => 'date',
            'delivery_oder_delivery_date' => 'date',
            'purchase_order_103_date' => 'date',
            'received_date' => 'date',
            'purchase_order_status_a_date' => 'date',
            'purchase_order_status_b_date' => 'date',
            'purchase_order_status_a_files' => 'array',
            'laprima_date' => 'date',
            'coa_date' => 'date',
            'coa_files' => 'array',
        ];
    }

    public function purchaseOrderIssued(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderIssued::class, 'purchase_order_terbit_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationReceiving::class, 'location_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(MonitoringNpkDetail::class);
    }
}
