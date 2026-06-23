<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrsRdtvItem extends Model
{
    protected $fillable = [
        'grs_rdtv_id',
        'delivery_order_receipt_id',
        'document_code',
        'file_path',
        'status',
        'reason',
    ];

    public function grsRdtv()
    {
        return $this->belongsTo(GrsRdtv::class);
    }

    public function deliveryOrderReceipt()
    {
        return $this->belongsTo(DeliveryOrderReceipt::class);
    }
}
