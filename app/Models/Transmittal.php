<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transmittal extends Model
{
    protected $fillable = [
        'transmittal_no',
        'type',
        'destination',
        'created_by',
        'created_at',
    ];

    public function transmittalItems()
    {
        return $this->hasMany(TransmittalItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($transmittal) {
            // Dapatkan ID DO yang terkait dengan transmittal ini
            $doIds = $transmittal->transmittalItems()->pluck('delivery_order_receipt_id');
            
            if ($doIds->isNotEmpty()) {
                // Hapus log QC history terkait yang memiliki referensi transmittal_no ini
                \App\Models\QcHistory::whereIn('delivery_order_receipt_id', $doIds)
                    ->where('notes', 'LIKE', "%No: {$transmittal->transmittal_no}%")
                    ->delete();
            }
            
            // Hapus juga transmittal items jika belum dicascade
            $transmittal->transmittalItems()->delete();
        });
    }
}
