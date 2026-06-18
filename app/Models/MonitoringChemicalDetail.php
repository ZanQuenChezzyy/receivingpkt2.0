<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'monitoring_chemical_id',
    'tahapan_qc_id',
    'quantity_received',
])]
class MonitoringChemicalDetail extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity_received' => 'decimal:2',
        ];
    }

    public function monitoringChemical(): BelongsTo
    {
        return $this->belongsTo(MonitoringChemical::class);
    }

    public function chemicalQcTuv(): BelongsTo
    {
        return $this->belongsTo(ChemicalQcTuv::class, 'tahapan_qc_id');
    }
}
