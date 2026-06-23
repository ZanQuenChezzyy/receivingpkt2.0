<?php

namespace App\Filament\Resources\MaterialIssues\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class MaterialIssueInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Informasi Utama')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('mir_number')->label('No. MIR')->weight(FontWeight::Bold)->color('primary')->copyable(),
                                TextEntry::make('tanggal')->label('Tanggal')->date('d F Y'),
                                TextEntry::make('purchaseOrderIssued.purchase_order_no')->label('Nomor PO')->icon('heroicon-m-shopping-cart'),
                                TextEntry::make('diminta_oleh')->label('Diminta Oleh'),
                                TextEntry::make('npk')->label('NPK'),
                                TextEntry::make('diterima_oleh')->label('Diterima Oleh'),
                                TextEntry::make('departemen')->label('Departemen'),
                                TextEntry::make('bagian')->label('Bagian'),
                                TextEntry::make('no_reservasi')->label('No. Reservasi'),
                                TextEntry::make('no_jor_wo')->label('No. JOR/WO'),
                                TextEntry::make('digunakan_untuk')->label('Digunakan Untuk')->columnSpanFull(),
                            ]),
                        ]),

                    Section::make('Detail Material Diambil')
                        ->icon('heroicon-o-cube')
                        ->schema([
                            RepeatableEntry::make('materialIssueDetails')
                                ->hiddenLabel()
                                ->schema([
                                    Grid::make(4)->schema([
                                        TextEntry::make('deliveryOrderReceiptDetail.material_code')->label('Kode Material')->weight(FontWeight::Bold),
                                        TextEntry::make('deliveryOrderReceiptDetail.description')->label('Deskripsi Material')->columnSpan(2),
                                        TextEntry::make('deliveryOrderReceiptDetail.locationReceiving.name')->label('Lokasi')->badge()->color('info'),

                                        TextEntry::make('diminta')->label('Qty Diminta')->color('warning')->weight(FontWeight::Bold)
                                            ->suffix(fn ($record) => " {$record->deliveryOrderReceiptDetail?->uoi}"),
                                        TextEntry::make('diserahkan')->label('Qty Diserahkan')->color('success')->weight(FontWeight::Bold)
                                            ->suffix(fn ($record) => " {$record->deliveryOrderReceiptDetail?->uoi}"),
                                        TextEntry::make('boh')->label('Sisa BOH Terakhir')->color('gray')
                                            ->suffix(fn ($record) => " {$record->deliveryOrderReceiptDetail?->uoi}"),
                                        TextEntry::make('stage_when_issued')->label('Stage saat Diambil')->badge()->color('info')->formatStateUsing(fn ($state) => $state ?: 'Default'),
                                    ]),
                                ]),
                        ]),
                        
                    Section::make('Tanda Tangan (Fisik / Digital)')
                        ->icon('heroicon-o-pencil')
                        ->schema([
                            Grid::make(6)->schema([
                                TextEntry::make('diminta_oleh')->label('Diminta Oleh'),
                                TextEntry::make('npk')->label('NPK'),
                                TextEntry::make('disetujui_oleh')->label('Disetujui Oleh (ISTEK)'),
                                TextEntry::make('diketahui_oleh')->label('Diketahui Oleh'),
                                TextEntry::make('diserahkan_oleh')->label('Diserahkan Oleh'),
                                TextEntry::make('diterima_oleh')->label('Diterima Oleh'),
                            ]),
                            Grid::make(2)->schema([
                                \Filament\Infolists\Components\ViewEntry::make('diminta_signature')
                                    ->label('Tanda Tangan Peminta (Digital)')
                                    ->view('filament.infolists.components.signature-display'),
                                \Filament\Infolists\Components\ViewEntry::make('disetujui_signature')
                                    ->label('Tanda Tangan ISTEK (Digital)')
                                    ->view('filament.infolists.components.signature-display'),
                            ]),
                        ]),
                ])->columnSpanFull(),
            ]);
    }
}
