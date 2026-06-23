<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialIssueDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_issue_id',
        'delivery_order_receipt_detail_id',
        'diminta',
        'diserahkan',
        'boh',
        'stage_when_issued',
    ];

    protected $casts = [
        'diminta' => 'decimal:2',
        'diserahkan' => 'decimal:2',
    ];

    public function materialIssue()
    {
        return $this->belongsTo(MaterialIssue::class);
    }

    public function deliveryOrderReceiptDetail()
    {
        return $this->belongsTo(DeliveryOrderReceiptDetail::class);
    }

    protected static function booted()
    {
        static::saving(function ($detail) {
            if (empty($detail->stage_when_issued) && $detail->delivery_order_receipt_detail_id) {
                $doReceiptDetail = DeliveryOrderReceiptDetail::with('deliveryOrderReceipt')->find($detail->delivery_order_receipt_detail_id);
                if ($doReceiptDetail && $doReceiptDetail->deliveryOrderReceipt) {
                    $detail->stage_when_issued = $doReceiptDetail->deliveryOrderReceipt->stage ?? 'ON_RECEIPT';
                }
            }
        });
    }
}
