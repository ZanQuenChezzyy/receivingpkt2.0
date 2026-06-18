<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QcHistory extends Model
{
    protected $guarded = [];

    public function deliveryOrderReceipt()
    {
        return $this->belongsTo(DeliveryOrderReceipt::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
