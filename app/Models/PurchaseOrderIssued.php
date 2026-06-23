<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'purchase_order_and_item',
    'material_type',
    'mrp_type',
    'purchase_order_no',
    'item_no',
    'material_code',
    'aac',
    'abc_indicator',
    'description',
    'qty_po',
    'uoi',
    'vendor_id',
    'vendor_name',
    'date_create',
    'delivery_date_po',
    'po_status',
    'incoterm',
    'currency',
    'net_price',
    'total_amount_in_lc',
    'requisitioner',
])]
class PurchaseOrderIssued extends Model
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
            'date_create' => 'date',
            'delivery_date_po' => 'date',
            'item_no' => 'integer',
            'qty_po' => 'decimal:0',
            'net_price' => 'decimal:0',
            'total_amount_in_lc' => 'decimal:0',
        ];
    }

    public function monitoringNpks(): HasMany
    {
        return $this->hasMany(MonitoringNpk::class);
    }

    public function monitoringChemicals(): HasMany
    {
        return $this->hasMany(MonitoringChemical::class);
    }

    public function chemicalQcTuvs(): HasMany
    {
        return $this->hasMany(ChemicalQcTuv::class);
    }

    public function deliveryOrderReceiptDetails(): HasMany
    {
        return $this->hasMany(DeliveryOrderReceiptDetail::class);
    }
}
