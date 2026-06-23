<?php

namespace App\Filament\Resources\MonitoringChemicals\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MonitoringChemicalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dokumen')->schema([
                    TextEntry::make('material_category'),
                    TextEntry::make('qc_by'),
                    TextEntry::make('do_number')->placeholder('-'),
                    TextEntry::make('receivedBy.name')->label('Penerima'),
                    TextEntry::make('received_date')->date('d F Y')->placeholder('-'),
                    TextEntry::make('doc_status')->badge(),
                ])->columns(3),

                Section::make('Detail Item Kedatangan')->schema([
                    RepeatableEntry::make('monitoringChemicalDetails')
                        ->schema([
                            TextEntry::make('purchaseOrderIssued.purchase_order_no')->label('PO Number'),
                            TextEntry::make('purchaseOrderIssued.description')->label('Deskripsi Item'),
                            TextEntry::make('quantity')->numeric(),
                            TextEntry::make('tahapan')->placeholder('-'),
                            TextEntry::make('chemicalQcTuv.tahapan_name')->label('Tahap TUV')->placeholder('-'),
                            TextEntry::make('locationReceiving.name')->label('Lokasi')->placeholder('-'),
                            IconEntry::make('is_qty_tolerance')->boolean(),
                            IconEntry::make('has_update_progress')->boolean(),
                            TextEntry::make('tanggal_pengajuan_simala')->date('d F Y')->placeholder('-'),
                            TextEntry::make('tanggal_pengambilan_sample')->date('d F Y')->placeholder('-'),
                            TextEntry::make('tanggal_terbit_coa')->date('d F Y')->placeholder('-'),
                            TextEntry::make('leadtime_coa')->numeric()->placeholder('-'),
                            TextEntry::make('notes')->columnSpanFull()->placeholder('-'),
                        ])->columns(4),
                ]),
            ]);
    }
}
