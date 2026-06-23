<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'delivery_order_receipt_id',
    'purchase_order_issued_id',
    'item_no',
    'quantity',
    'material_code',
    'description',
    'uoi',
    'mrp_type',
    'material_type',
    'aac',
    'abc_indicator',
    'requisitioner',
    'total_amount_snapshot',
    'location_id',
    'is_different_location',
    'is_qty_tolerance',
])]
class DeliveryOrderReceiptDetail extends Model
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
            'item_no' => 'integer',
            'quantity' => 'decimal:0',
            'total_amount_snapshot' => 'decimal:0',
            'is_different_location' => 'boolean',
            'is_qty_tolerance' => 'boolean',
        ];
    }

    public function deliveryOrderReceipt(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrderReceipt::class);
    }

    public function purchaseOrderIssued(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderIssued::class);
    }

    public function locationReceiving(): BelongsTo
    {
        return $this->belongsTo(LocationReceiving::class, 'location_id');
    }

    public function materialIssueDetails()
    {
        return $this->hasMany(MaterialIssueDetail::class);
    }

    public function getIssuedQuantityAttribute()
    {
        return $this->materialIssueDetails()->sum('diserahkan');
    }

    protected static function booted()
    {
        static::saving(function ($detail) {
            if ($detail->purchase_order_issued_id && $detail->quantity !== null) {
                // Ensure purchaseOrderIssued relation is loaded or load it
                $po = $detail->purchaseOrderIssued;
                if ($po) {
                    $unitPriceLc = ($po->qty_po > 0) ? ((float) $po->total_amount_in_lc / (float) $po->qty_po) : (float) $po->net_price;
                    $detail->total_amount_snapshot = (float) $detail->quantity * $unitPriceLc;
                }
            }
        });
    }
}
