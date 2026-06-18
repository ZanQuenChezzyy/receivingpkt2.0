<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'monitoring_npk_id',
    'item_no',
    'material_code',
    'description',
    'quantity',
    'uoi',
    'location_id',
    'is_qty_tolerance',
])]
class MonitoringNpkDetail extends Model
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
            'quantity' => 'decimal:2',
            'is_qty_tolerance' => 'boolean',
        ];
    }

    public function monitoringNpk(): BelongsTo
    {
        return $this->belongsTo(MonitoringNpk::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationReceiving::class, 'location_id');
    }
}
