<?php

namespace App\Filament\Resources\MaterialIssues\Schemas;

use App\Models\DeliveryOrderReceiptDetail;
use App\Models\PurchaseOrderIssued;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MaterialIssueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Informasi Utama MIR')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->description('Data utama formulir Material Issued Request (MIR).')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('mir_number')
                                    ->label('No. MIR')
                                    ->disabled()
                                    ->placeholder('Auto Generated')
                                    ->dehydrated(false)
                                    ->visibleOn('create'),
                                TextInput::make('mir_number')
                                    ->label('No. MIR')
                                    ->disabled()
                                    ->visibleOn(['edit', 'view']),
                                DatePicker::make('tanggal')
                                    ->label('Tanggal')
                                    ->required()
                                    ->native(false)
                                    ->default(now()),
                                Select::make('purchase_order_issued_id')
                                    ->label('Nomor PO')
                                    ->searchable()
                                    ->preload(false)
                                    ->required()
                                    ->getSearchResultsUsing(function (string $search) {
                                        return PurchaseOrderIssued::whereHas('deliveryOrderReceiptDetails')
                                            ->where('purchase_order_no', 'like', "%{$search}%")
                                            ->limit(50)
                                            ->get()
                                            ->unique('purchase_order_no')
                                            ->pluck('purchase_order_no', 'id')
                                            ->toArray();
                                    })
                                    ->getOptionLabelUsing(fn ($value): ?string => PurchaseOrderIssued::find($value)?->purchase_order_no)
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('materialIssueDetails', [])),
                            ]),
                            Grid::make(3)->schema([
                                TextInput::make('no_hp')->label('No. Hp')->required(),
                                TextInput::make('departemen')->label('Departemen')->required(),
                                TextInput::make('bagian')->label('Bagian')->required(),
                                TextInput::make('no_reservasi')->label('No. Reservasi'),
                                TextInput::make('no_jor_wo')->label('No. JOR/WO'),
                                TextInput::make('no_alat')->label('No. Alat'),
                                TextInput::make('kode_biaya')->label('Kode Biaya'),
                            ]),
                            Textarea::make('digunakan_untuk')->label('Digunakan Untuk')->required()->columnSpanFull(),
                        ]),

                    Section::make('Detail Barang yang Diambil')
                        ->icon(Heroicon::OutlinedCube)
                        ->description('Pilih barang dari PO yang dipilih beserta jumlah kuantitas yang diambil.')
                        ->schema([
                            Repeater::make('materialIssueDetails')
                                ->relationship()
                                ->label('') // Disembunyikan karena sudah ada judul Section
                                ->addActionLabel('Tambah Material')
                                ->collapsible()
                                ->defaultItems(1)
                                ->schema([
                                    Grid::make(12)->schema([
                                        Select::make('delivery_order_receipt_detail_id')
                                            ->label('Item No.')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(4)
                                            ->prefixIcon('heroicon-m-hashtag')
                                            ->options(function (Get $get) {
                                                $poId = $get('../../purchase_order_issued_id');
                                                if (! $poId) {
                                                    return [];
                                                }
                                                $poItem = PurchaseOrderIssued::find($poId);
                                                if (! $poItem) {
                                                    return [];
                                                }
                                                $allPoItemIds = PurchaseOrderIssued::where('purchase_order_no', $poItem->purchase_order_no)->pluck('id');

                                                return DeliveryOrderReceiptDetail::whereIn('purchase_order_issued_id', $allPoItemIds)
                                                    ->get()
                                                    ->mapWithKeys(function ($detail) {
                                                        // Tampilkan Item No. beserta Deskripsinya agar lebih informatif
                                                        return [$detail->id => "Item {$detail->item_no}"];
                                                    });
                                            })
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                if ($state) {
                                                    $detail = DeliveryOrderReceiptDetail::with('locationReceiving')->find($state);
                                                    if ($detail) {
                                                        $set('description', $detail->description);
                                                        $set('stock_no', $detail->material_code);
                                                        $set('location', $detail->locationReceiving?->name);
                                                        $set('uoi', $detail->uoi);

                                                        // BOH = Qty Datang - Qty yang sudah diambil sebelumnya
                                                        $qtyReceived = (float) $detail->quantity;
                                                        $qtyIssued = (float) $detail->issued_quantity;
                                                        $set('boh', $qtyReceived - $qtyIssued);
                                                    }
                                                } else {
                                                    $set('description', null);
                                                    $set('stock_no', null);
                                                    $set('location', null);
                                                    $set('uoi', null);
                                                    $set('boh', null);
                                                }
                                            }),

                                        TextInput::make('stock_no')
                                            ->label('Stock No')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->columnSpan(3),

                                        TextInput::make('description')
                                            ->label('Deskripsi Material')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->columnSpan(5),

                                        TextInput::make('location')
                                            ->label('Lokasi Penyimpanan')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->prefixIcon('heroicon-m-map-pin')
                                            ->columnSpan(4),

                                        // Hidden UOI untuk kebutuhan suffix
                                        Hidden::make('uoi')->dehydrated(false),

                                        TextInput::make('diminta')
                                            ->label('Qty Diminta')
                                            ->numeric()
                                            ->required()
                                            ->suffix(fn (Get $get) => $get('uoi') ?? '')
                                            ->rule(function (Get $get) {
                                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                    $boh = (float) $get('boh');
                                                    if ((float) $value > $boh) {
                                                        $fail("Kuantitas tidak boleh melebihi sisa stok (BOH: {$boh}).");
                                                    }
                                                };
                                            })
                                            ->columnSpan(3),

                                        TextInput::make('diserahkan')
                                            ->label('Qty Diserahkan')
                                            ->numeric()
                                            ->required()
                                            ->suffix(fn (Get $get) => $get('uoi') ?? '')
                                            ->rule(function (Get $get) {
                                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                    $boh = (float) $get('boh');
                                                    if ((float) $value > $boh) {
                                                        $fail("Kuantitas tidak boleh melebihi sisa stok (BOH: {$boh}).");
                                                    }
                                                };
                                            })
                                            ->columnSpan(3),

                                        TextInput::make('boh')
                                            ->label('Sisa BOH (Jika Ada)')
                                            ->numeric()
                                            ->readOnly()
                                            ->suffix(fn (Get $get) => $get('uoi') ?? '')
                                            ->columnSpan(2),
                                    ]),
                                ])
                                ->columnSpanFull()
                                ->hidden(fn (Get $get): bool => empty($get('purchase_order_issued_id'))),
                        ]),

                    Section::make('Tanda Tangan (Fisik / Digital)')
                        ->icon(Heroicon::OutlinedPencil)
                        ->description('Catat nama pihak yang bertanda tangan di formulir fisik atau lihat tanda tangan digital MIR.')
                        ->schema([
                            Grid::make(6)->schema([
                                TextInput::make('diminta_oleh')->label('Diminta Oleh'),
                                TextInput::make('npk')->label('NPK'),
                                TextInput::make('disetujui_oleh')->label('Disetujui Oleh (ISTEK)'),
                                TextInput::make('diketahui_oleh')->label('Diketahui Oleh'),
                                TextInput::make('diserahkan_oleh')->label('Diserahkan (Receiving)'),
                                TextInput::make('diterima_oleh')->label('Diterima Oleh'),
                            ]),
                            Grid::make(2)->schema([
                                Placeholder::make('diminta_signature')
                                    ->label('Tanda Tangan Peminta (Digital)')
                                    ->content(fn ($record) => $record?->diminta_signature ? new \Illuminate\Support\HtmlString('<img src="'.$record->diminta_signature.'" style="max-height: 100px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 4px; background: white;">') : 'Tidak Ada Tanda Tangan Digital'),
                                Placeholder::make('disetujui_signature')
                                    ->label('Tanda Tangan ISTEK (Digital)')
                                    ->content(fn ($record) => $record?->disetujui_signature ? new \Illuminate\Support\HtmlString('<img src="'.$record->disetujui_signature.'" style="max-height: 100px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 4px; background: white;">') : 'Tidak Ada Tanda Tangan Digital'),
                            ]),
                        ]),
                ])->columnSpanFull(),
            ]);
    }
}
