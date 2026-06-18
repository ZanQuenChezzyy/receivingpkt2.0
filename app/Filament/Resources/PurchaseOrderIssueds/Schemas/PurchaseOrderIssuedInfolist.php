<?php

namespace App\Filament\Resources\PurchaseOrderIssueds\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PurchaseOrderIssuedInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: Informasi Utama Dokumen PO
                Section::make('Informasi Purchase Order')
                    ->description('Detail utama dari dokumen Purchase Order.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2, 'md' => 3])->schema([
                            TextEntry::make('purchase_order_no')
                                ->label('Nomor PO')
                                ->weight('bold')
                                ->color('primary')
                                ->copyable(),
                            TextEntry::make('item_no')
                                ->label('Item No.'),
                            TextEntry::make('purchase_order_and_item')
                                ->label('PO & Item')
                                ->placeholder('-'),

                            TextEntry::make('po_status')
                                ->label('Status PO')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'A' => 'success',
                                    'B' => 'warning',
                                    'C' => 'danger',
                                    default => 'gray',
                                })
                                ->placeholder('-'),
                            TextEntry::make('date_create')
                                ->label('Tanggal Dibuat')
                                ->date('d M Y')
                                ->icon('heroicon-m-calendar'),
                            TextEntry::make('delivery_date_po')
                                ->label('Estimasi Pengiriman')
                                ->date('d M Y')
                                ->icon('heroicon-m-truck')
                                ->placeholder('-'),

                            TextEntry::make('requisitioner')
                                ->label('Requisitioner')
                                ->icon('heroicon-m-user')
                                ->columnSpan(['default' => 1, 'md' => 3]),
                        ]),
                    ])->collapsible(),

                // SECTION 2: Detail Material yang dipesan
                Section::make('Detail Material')
                    ->description('Spesifikasi barang, kuantitas, dan kode material terkait (ZSP, ZSM, ZRM, dll).')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Deskripsi Material')
                            ->weight('bold')
                            ->columnSpanFull(),

                        Grid::make(['default' => 1, 'sm' => 2, 'md' => 3])->schema([
                            TextEntry::make('material_code')
                                ->label('Kode Material')
                                ->fontFamily('mono')
                                ->copyable()
                                ->placeholder('-'),
                            TextEntry::make('material_type')
                                ->label('Tipe Material')
                                ->placeholder('-'),
                            TextEntry::make('qty_po')
                                ->label('Kuantitas (PO)')
                                ->numeric()
                                ->weight('bold'),
                            TextEntry::make('uoi')
                                ->label('Satuan (UoI)'),

                            TextEntry::make('mrp_type')
                                ->label('MRP Type')
                                ->placeholder('-'),
                            TextEntry::make('aac')
                                ->label('AAC')
                                ->placeholder('-'),
                            TextEntry::make('abc_indicator')
                                ->label('Indikator ABC')
                                ->placeholder('-'),

                            // PENAMBAHAN: Net Price per item
                            TextEntry::make('net_price')
                                ->label('Net Price')
                                // Mengambil currency dinamis dari record, atau fallback ke IDR
                                ->money(fn ($record) => $record->currency ?? 'IDR')
                                ->weight('bold')
                                ->color('primary'),
                        ]),
                    ])->collapsible(),

                // SECTION 3: Data Vendor dan Komersial
                Section::make('Informasi Vendor & Finansial')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 2])->schema([
                            Group::make([
                                TextEntry::make('vendor_name')
                                    ->label('Nama Vendor')
                                    ->weight('bold')
                                    ->icon('heroicon-m-building-office-2'),
                                TextEntry::make('vendor_id')
                                    ->label('ID Vendor')
                                    ->placeholder('-')
                                    ->color('gray'),
                            ]),
                            Group::make([
                                // PENAMBAHAN: Explicit Currency field
                                TextEntry::make('currency')
                                    ->label('Mata Uang')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('-'),

                                TextEntry::make('incoterm')
                                    ->label('Incoterm')
                                    ->placeholder('-')
                                    ->columnSpanFull(),

                                TextEntry::make('total_amount_in_lc')
                                    ->label('Total Amount (LC)')
                                    ->money(fn ($record) => $record->currency ?? 'IDR')
                                    ->color('success')
                                    ->weight('bold')
                                    ->columnSpanFull(),
                            ])->columns(2), // Membagi group menjadi 2 kolom agar currency dan total amount sejajar
                        ]),
                    ])->collapsible(),

                // SECTION 4: System Timestamps
                Section::make('Informasi Sistem')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('created_at')
                                ->label('Dibuat Pada')
                                ->dateTime()
                                ->placeholder('-'),
                            TextEntry::make('updated_at')
                                ->label('Terakhir Diperbarui')
                                ->dateTime()
                                ->placeholder('-'),
                        ]),
                    ])->collapsed(),
            ]);
    }
}
