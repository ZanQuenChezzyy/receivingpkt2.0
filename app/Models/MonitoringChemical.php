<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoringChemical extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_category',
        'qc_by',
        'do_number',
        'document_path',
        'received_by',
        'received_date',
        'doc_status',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'received_date' => 'date',
        ];
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function monitoringChemicalDetails(): HasMany
    {
        return $this->hasMany(MonitoringChemicalDetail::class);
    }
}
