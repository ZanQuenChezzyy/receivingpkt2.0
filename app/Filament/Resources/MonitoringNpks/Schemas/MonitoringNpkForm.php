<?php

namespace App\Filament\Resources\MonitoringNpks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class MonitoringNpkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(12)->schema([
                    // === KOLOM KIRI: PO & Dokumen + Timeline ===
                    Group::make()->schema([
                        Section::make('Data Dokumen Monitoring')
                            ->description('Pilih Nomor PO untuk memuat item otomatis. Lengkapi Nomor DO, Tahapan proses, dan Lokasi penerimaan.')
                            ->icon('heroicon-m-bars-3-bottom-left')
                            ->columns(12)
                            ->schema([
                                Select::make('purchase_order_terbit_id')
                                    ->label('Nomor Purchase Order')
                                    ->placeholder('Pilih Nomor PO')
                                    ->relationship(
                                        name: 'purchaseOrderIssued',
                                        titleAttribute: 'purchase_order_no',
                                        modifyQueryUsing: fn($query) => $query
                                            ->selectRaw('MIN(id) as id, purchase_order_no')
                                            ->groupBy('purchase_order_no')
                                            ->orderBy('purchase_order_no')
                                    )
                                    ->searchable()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function (mixed $state, Set $set): void {
                                        if (!$state) {
                                            $set('details', [[]]);
                                            return;
                                        }

                                        $anchor = \App\Models\PurchaseOrderIssued::find($state);
                                        if (!$anchor) {
                                            $set('details', [[]]);
                                            return;
                                        }

                                        $items = \App\Models\PurchaseOrderIssued::where('purchase_order_no', $anchor->purchase_order_no)
                                            ->orderBy('item_no')
                                            ->get(['id', 'item_no', 'material_code', 'description', 'qty_po', 'uoi']);

                                        $set('details', $items->map(fn($it) => [
                                            'purchase_order_issued_id' => $it->id,
                                            'item_no' => $it->item_no,
                                            'material_code' => $it->material_code,
                                            'description' => $it->description,
                                            'quantity' => $it->qty_po,
                                            'uoi' => $it->uoi,
                                        ])->toArray());
                                    })
                                    ->columnSpan(6),

                                TextInput::make('delivery_oder_number')
                                    ->label('Nomor DO')
                                    ->placeholder('Masukkan Nomor DO')
                                    ->maxLength(30)
                                    ->required()
                                    ->rule(fn (Get $get, ?\App\Models\MonitoringNpk $record) =>
                                        \Illuminate\Validation\Rule::unique('monitoring_npks', 'delivery_oder_number')
                                            ->where(fn ($q) => $q->where('purchase_order_terbit_id', (int) $get('purchase_order_terbit_id')))
                                            ->ignore($record?->getKey())
                                    )
                                    ->dehydrateStateUsing(function (?string $state) {
                                        $s = trim((string) $state);
                                        return $s === '' || $s === '-' ? null : $s;
                                    })
                                    ->helperText('Unik per Nomor PO.')
                                    ->columnSpan(6),

                                TextInput::make('stage')
                                    ->label('Tahapan')
                                    ->placeholder('Masukkan Tahapan')
                                    ->maxLength(100)
                                    ->columnSpan(6),

                                Select::make('location_id')
                                    ->label('Lokasi')
                                    ->placeholder('Pilih Lokasi')
                                    ->relationship('location', 'name')
                                    ->default(125)
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->columnSpan(6),
                            ]),

                        Section::make('Timeline Proses')
                            ->description('Catat urutan tanggal penerimaan material dan dokumen secara berurutan.')
                            ->icon('heroicon-m-calendar-days')
                            ->columns(12)
                            ->schema([
                                DatePicker::make('sample_receivied_date')
                                    ->label('Terima Sample')
                                    ->placeholder('Pilih tanggal')
                                    ->native(false)
                                    ->helperText('Tanggal sample pertama kali diterima oleh tim.')
                                    ->prefixIcon('heroicon-m-beaker')
                                    ->columnSpan(6),

                                DatePicker::make('delivery_oder_delivery_date')
                                    ->label('DO Dikirim')
                                    ->placeholder('Pilih tanggal')
                                    ->live()
                                    ->native(false)
                                    ->helperText('Tanggal dokumen Delivery Order mulai dikirim.')
                                    ->prefixIcon('heroicon-m-truck')
                                    ->columnSpan(6),

                                DatePicker::make('received_date')
                                    ->label('Penerimaan (Actual)')
                                    ->placeholder('Pilih tanggal')
                                    ->visible(fn(Get $get) => filled($get('delivery_oder_delivery_date')))
                                    ->required(fn(Get $get) => filled($get('delivery_oder_delivery_date')))
                                    ->native(false)
                                    ->helperText('Tanggal material fisik tiba di lokasi.')
                                    ->prefixIcon('heroicon-m-inbox-arrow-down')
                                    ->columnSpan(6),

                                DatePicker::make('purchase_order_103_date')
                                    ->label('Proses 103')
                                    ->placeholder('Pilih tanggal')
                                    ->native(false)
                                    ->helperText('Tanggal verifikasi proses 103 (Goods Receipt).')
                                    ->prefixIcon('heroicon-m-document-check')
                                    ->columnSpan(6),

                                DatePicker::make('laprima_date')
                                    ->label('Terbit LAPRIMA')
                                    ->placeholder('Pilih tanggal')
                                    ->native(false)
                                    ->helperText('Tanggal dokumen Laporan Penerimaan Material terbit.')
                                    ->prefixIcon('heroicon-m-clipboard-document')
                                    ->columnSpan(6),

                                DatePicker::make('coa_date')
                                    ->label('Terima COA')
                                    ->placeholder('Pilih tanggal')
                                    ->live()
                                    ->native(false)
                                    ->helperText('Tanggal Certificate of Analysis (COA) diterima.')
                                    ->prefixIcon('heroicon-m-shield-check')
                                    ->columnSpan(6),

                                FileUpload::make('coa_files')
                                    ->label('Dokumen COA (PDF)')
                                    ->multiple()
                                    ->appendFiles()
                                    ->directory('monitoring-npk-docs')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->downloadable()
                                    ->openable()
                                    ->helperText('Unggah salinan dokumen COA sebagai bukti.')
                                    ->visible(fn(Get $get) => filled($get('coa_date')))
                                    ->required(fn(Get $get) => filled($get('coa_date')))
                                    ->columnSpan(6),
                            ]),
                    ])->columnSpan(['lg' => 7]),

                    // === KOLOM KANAN: DETAIL ITEM ===
                    Group::make()->schema([
                        Section::make('Detail Item')
                            ->description('Wajib pilih nomor PO terlebih dahulu. Atur kuantitas sesuai DO aktual.')
                            ->icon('heroicon-m-clipboard-document-list')
                            ->schema([
                               Repeater::make('details')
                                    ->hiddenLabel()
                                    ->relationship('details')
                                    ->addActionLabel('Tambah Item')
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->disabled(fn(Get $get) => blank($get('purchase_order_terbit_id')))
                                    ->addable(fn(Get $get) => filled($get('purchase_order_terbit_id')))
                                    ->deletable(fn(Get $get) => filled($get('purchase_order_terbit_id')))
                                    ->schema([
                                        Grid::make(12)->schema([
                                            Select::make('purchase_order_issued_id')
                                                ->label('Item No')
                                                ->placeholder('Pilih Item')
                                                ->options(function (Get $get) {
                                                    $anchorId = $get('../../purchase_order_terbit_id');
                                                    if (!$anchorId) return [];
                                                    $anchor = \App\Models\PurchaseOrderIssued::find($anchorId);
                                                    if (!$anchor) return [];

                                                    return \App\Models\PurchaseOrderIssued::where('purchase_order_no', $anchor->purchase_order_no)
                                                        ->orderBy('item_no')
                                                        ->get()
                                                        ->mapWithKeys(fn($r) => [
                                                            $r->id => str_pad((string) $r->item_no, 2, '0', STR_PAD_LEFT),
                                                        ])->all();
                                                })
                                                ->searchable()
                                                ->live()
                                                ->columnSpan(4)
                                                ->afterStateUpdated(function ($state, Set $set) {
                                                    $po = \App\Models\PurchaseOrderIssued::find($state);
                                                    $set('item_no', $po?->item_no);
                                                    $set('material_code', $po?->material_code);
                                                    $set('description', $po?->description);
                                                    $set('uoi', $po?->uoi);
                                                    if ($po?->qty_po) {
                                                        $set('quantity', $po->qty_po);
                                                    }
                                                })
                                                ->rule(function (Get $get) {
                                                    return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                        $rows = $get('../*') ?? [];
                                                        $ids = collect($rows)->pluck('purchase_order_issued_id')->filter()->all();
                                                        if (count($ids) !== count(array_unique($ids))) {
                                                            $fail('Item yang sama tidak boleh dipilih dua kali.');
                                                        }
                                                    };
                                                }),

                                            Hidden::make('item_no')->dehydrated(true),
                                            Hidden::make('uoi')->dehydrated(true),

                                            TextInput::make('material_code')
                                                ->label('Mat. Code')
                                                ->disabled()
                                                ->dehydrated(true)
                                                ->columnSpan(8),

                                            TextInput::make('description')
                                                ->label('Deskripsi')
                                                ->disabled()
                                                ->dehydrated(true)
                                                ->columnSpan(12),
                                                
                                            TextInput::make('quantity')
                                                ->label('Quantity Aktual')
                                                ->placeholder('0.00')
                                                ->suffix(fn(Get $get) => $get('uoi') ?: null)
                                                ->numeric()
                                                ->minValue(0.01)
                                                ->columnSpan(fn () => optional(Auth::user())->hasRole(['Developer', 'Super Admin', 'Staff', 'Admin']) ? 8 : 12)
                                                ->rules([
                                                    fn(Get $get, $record): \Closure =>
                                                    function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                                        $rowPoTerbitId = (int) ($get('purchase_order_issued_id') ?? 0);
                                                        $itemNo = $get('item_no');
                                                        if (!$rowPoTerbitId || !$itemNo) return;

                                                        $isTolerance = (bool) ($get('is_qty_tolerance') ?? false);

                                                        $currentMonitoringId = $record?->getAttribute('monitoring_npk_id');
                                                        $h = \App\Filament\Resources\MonitoringNpks\MonitoringNpkResource::hitungSisaDbByItem($rowPoTerbitId, $currentMonitoringId);
                                                        $poQty = (float) ($h['po'] ?? 0);
                                                        $usedDb = (float) ($h['used_db'] ?? 0);
                                                        $uoi = (string) ($h['uoi'] ?? '');

                                                        $rows = $get('../../details') ?? [];
                                                        $inFormSum = collect($rows)
                                                            ->where('item_no', $itemNo)
                                                            ->sum(fn($r) => (float) ($r['quantity'] ?? 0));

                                                        if (!$isTolerance && ($usedDb + $inFormSum) > $poQty) {
                                                            $sisa = max(0, $poQty - $usedDb);
                                                            $fail("Kuantitas melebihi sisa {$sisa} {$uoi}. Aktifkan 'Toleransi Qty' bila lebih.");
                                                        }

                                                        if ((float) $value <= 0) {
                                                            $fail('Quantity harus lebih dari 0.');
                                                        }
                                                    }
                                                ]),

                                           Toggle::make('is_qty_tolerance')
                                                ->label('Toleransi Qty?')
                                                ->inline(false)
                                                ->onColor('danger')
                                                ->live()
                                                ->hidden(fn() => !optional(Auth::user())->hasRole(['Developer', 'Super Admin', 'Staff', 'Admin']))
                                                ->default(false)
                                                ->dehydrated()
                                                ->columnSpan(4),
                                                
                                           TextEntry::make('sisa_info')
                                                ->hiddenLabel()
                                                ->state(function (Get $get, $record) {
                                                    $rowPoTerbitId = (int) ($get('purchase_order_issued_id') ?? 0);
                                                    if ($rowPoTerbitId === 0) {
                                                        return '-';
                                                    }
                                                    $currentMonitoringId = $record?->getAttribute('monitoring_npk_id');
                                                    $h = \App\Filament\Resources\MonitoringNpks\MonitoringNpkResource::hitungSisaDbByItem($rowPoTerbitId, $currentMonitoringId);
                                                
                                                    $po     = (float) ($h['po'] ?? 0);
                                                    $usedDb = (float) ($h['used_db'] ?? 0);
                                                    $uoi    = (string) ($h['uoi'] ?? '');
                                                    $sisa   = max(0, $po - $usedDb);
                                                    
                                                    $sisaText = number_format($sisa, 2, ',', '.') . " " . $uoi;
                                                    
                                                    if ($sisa > 0) {
                                                        return new \Illuminate\Support\HtmlString("<span class='text-sm font-semibold text-danger-600'>Sisa: {$sisaText}</span> <span class='text-xs text-gray-500'>(Target PO: {$po} {$uoi})</span>");
                                                    }
                                                    
                                                    return new \Illuminate\Support\HtmlString("<span class='text-sm font-semibold text-success-600'>Kuota PO Terpenuhi</span>");
                                                })
                                                ->html()
                                                ->columnSpan(12),
                                        ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Status Purchase Order')
                            ->icon('heroicon-m-clipboard-document-check')
                            ->description('A (Sesuai PO) / B (Selesai).')
                            ->columns(12)
                            ->schema([
                               ToggleButtons::make('purchase_order_status')
                                    ->label('Status PO (terkini)')
                                    ->options(['A' => 'A / Kosong', 'B' => 'B / Selesai'])
                                    ->icons(['A' => 'heroicon-m-x-circle', 'B' => 'heroicon-m-check-circle'])
                                    ->colors(['A' => 'danger', 'B' => 'success'])
                                    ->grouped()
                                    ->inline()
                                    ->live()
                                    ->extraAttributes(['class' => 'mx-auto flex justify-center w-full'])
                                    ->afterStateUpdated(function (string $state, Set $set, Get $get) {
                                        if ($state === 'B' && blank($get('purchase_order_status_b_date'))) {
                                            $set('purchase_order_status_b_date', now()->toDateString());
                                        }
                                        if ($state === 'A' && blank($get('purchase_order_status_a_date'))) {
                                            $set('purchase_order_status_a_date', now()->toDateString());
                                        }
                                    })
                                    ->columnSpan(12),

                               Fieldset::make('Status A')
                                    ->columns(12)
                                    ->schema([
                                        DatePicker::make('purchase_order_status_a_date')
                                            ->label('Tanggal Status A')
                                            ->native(false)
                                            ->disabled(fn(Get $get) => blank($get('purchase_order_status')) || $get('purchase_order_status') === 'B')
                                            ->required(fn(Get $get) => $get('purchase_order_status') === 'A')
                                            ->columnSpan(6),

                                        DatePicker::make('purchase_order_status_b_date')
                                            ->label('Tanggal Status B')
                                            ->native(false)
                                            ->disabled(fn(Get $get) => blank($get('purchase_order_status')) || $get('purchase_order_status') === 'A')
                                            ->required(fn(Get $get) => $get('purchase_order_status') === 'B')
                                            ->columnSpan(6),

                                        FileUpload::make('purchase_order_status_a_files')
                                            ->label('Evidence Status A')
                                            ->multiple()
                                            ->appendFiles()
                                            ->directory('monitoring-npk-docs')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->disabled(fn(Get $get) => blank($get('purchase_order_status')) || $get('purchase_order_status') === 'B')
                                            ->required(fn(Get $get) => $get('purchase_order_status') === 'A')
                                            ->columnSpan(12),
                                    ])
                                    ->columnSpan(12),
                            ]),
                        
                        Hidden::make('created_by')
                            ->default(fn () => Auth::id() ?? 1)
                            ->required(),
                    ])->columnSpan(['lg' => 5]),
                ])->columnSpanFull(),
            ]);
    }
}
