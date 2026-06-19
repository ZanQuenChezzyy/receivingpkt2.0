<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrsRdtv extends Model
{
    protected $fillable = [
        'transaction_date',
        'category',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function grsRdtvItems()
    {
        return $this->hasMany(GrsRdtvItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
