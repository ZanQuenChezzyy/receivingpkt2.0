<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MaterialIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'mir_number',
        'tanggal',
        'purchase_order_issued_id',
        'no_hp',
        'no_reservasi',
        'departemen',
        'bagian',
        'no_jor_wo',
        'digunakan_untuk',
        'no_alat',
        'kode_biaya',
        'diminta_oleh',
        'npk',
        'diminta_signature',
        'disetujui_oleh',
        'disetujui_signature',
        'diketahui_oleh',
        'diserahkan_oleh',
        'diterima_oleh',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function purchaseOrderIssued()
    {
        return $this->belongsTo(PurchaseOrderIssued::class);
    }

    public function materialIssueDetails()
    {
        return $this->hasMany(MaterialIssueDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->created_by) && Auth::check()) {
                $model->created_by = Auth::id();
            }

            if (empty($model->mir_number)) {
                $month = date('Ym');
                $lastRecord = self::where('mir_number', 'LIKE', "MIR/{$month}/%")->orderBy('id', 'desc')->first();
                $nextId = 1;
                if ($lastRecord) {
                    $parts = explode('/', $lastRecord->mir_number);
                    $nextId = (int) end($parts) + 1;
                }
                $model->mir_number = "MIR/{$month}/" . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
