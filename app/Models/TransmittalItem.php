<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransmittalItem extends Model
{
    protected $guarded = [];

    public function transmittal()
    {
        return $this->belongsTo(Transmittal::class);
    }

    public function deliveryOrderReceipt()
    {
        return $this->belongsTo(DeliveryOrderReceipt::class);
    }
}
