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

    protected static function booted()
    {
        static::created(function ($qcHistory) {
            $doReceipt = $qcHistory->deliveryOrderReceipt;
            if ($doReceipt) {
                if ($qcHistory->status === 'Kirim') {
                    $doReceipt->status = 'Pengajuan QC';
                } elseif ($qcHistory->status === 'Kembali') {
                    $doReceipt->status = 'Kembali dari ISTEK';
                } elseif ($qcHistory->status === 'Revisi') {
                    $doReceipt->status = 'Pending (Menunggu Pengajuan Ulang)';
                }

                $doReceipt->save();
            }
        });
    }
}
