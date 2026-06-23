<?php

namespace App\Filament\Resources\MonitoringChemicals\Schemas;

use App\Models\ChemicalQcTuv;
use App\Models\MonitoringChemicalDetail;
use App\Models\PurchaseOrderIssued;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class MonitoringChemicalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(12)->schema([
                Hidden::make('created_by')->default(fn () => Auth::id()),

                Group::make()->schema([
                    self::getInformasiKedatanganSection(),
                ])->columnSpan(['lg' => 5]),

                Group::make()->schema([
                    self::getDetailItemKedatanganSection(),
                ])->columnSpan(['lg' => 7]),
                self::getSummaryPoSection()
                    ->columnSpanFull(),
            ])->columnSpanFull(),
        ]);
    }

    protected static function getInformasiKedatanganSection(): Section
    {
        return Section::make('Informasi Dokumen Kedatangan')
            ->icon(Heroicon::OutlinedTruck)
            ->description('Pilih kategori dan lengkapi detail dokumen penerimaan barang.')
            ->schema([
                Grid::make(3)
                    ->schema([
                        ToggleButtons::make('material_category')
                            ->label('Kategori Material')
                            ->options([
                                'Karung' => 'Karung',
                                'Chemical' => 'Chemical',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->colors([
                                'Karung' => Color::Yellow,
                                'Chemical' => Color::Blue,
                                'Lainnya' => Color::Emerald,
                            ])
                            ->inline()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state === 'Karung') {
                                    $set('qc_by', 'ISTEK');
                                } elseif ($state === 'Chemical') {
                                    $set('qc_by', 'PPE');
                                } else {
                                    $set('qc_by', null);
                                }
                                $set('search_po', null);
                                $set('monitoringChemicalDetails', []);
                            })
                            ->columnSpan(2),

                        ToggleButtons::make('qc_by')
                            ->label('Tujuan QC')
                            ->options([
                                'ISTEK' => 'ISTEK',
                                'PPE' => 'PPE',
                            ])
                            ->colors([
                                'ISTEK' => Color::Blue,
                                'PPE' => Color::Indigo,
                            ])
                            ->inline()
                            ->required(),
                    ]),
                Select::make('search_po')
                    ->label('Pilih PO')
                    ->placeholder('Cari Nomor PO...')
                    ->disabled(fn (Get $get) => blank($get('material_category')))
                    ->helperText(fn (Get $get) => blank($get('material_category')) ? 'Pilih kategori material terlebih dahulu.' : null)
                    ->options(function (Get $get) {
                        $cat = $get('material_category');
                        $query = PurchaseOrderIssued::query();
                        if ($cat === 'Karung' || $cat === 'Chemical') {
                            $query->whereIn('material_type', ['ZSM', 'Chemical', 'CHEMICAL', 'CHM']);
                        }

                        return $query->select('purchase_order_no')
                            ->distinct()
                            ->pluck('purchase_order_no', 'purchase_order_no');
                    })
                    ->searchable()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Select $component, $record) {
                        if ($record) {
                            $firstDetail = $record->monitoringChemicalDetails()->first();
                            if ($firstDetail) {
                                $poItem = PurchaseOrderIssued::find($firstDetail->purchase_order_issued_id);
                                if ($poItem) {
                                    $component->state($poItem->purchase_order_no);
                                }
                            }
                        }
                    })
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state, $record) {
                        if (! $state) {
                            $set('monitoringChemicalDetails', []);

                            return;
                        }

                        $cat = $get('material_category');
                        $poItems = PurchaseOrderIssued::where('purchase_order_no', $state)->get();
                        $repeaterData = [];
                        $recordId = $record ? $record->id : null;

                        foreach ($poItems as $item) {
                            $historyQty = MonitoringChemicalDetail::where('purchase_order_issued_id', $item->id)
                                ->when($recordId, function ($q) use ($recordId) {
                                    $q->whereHas('monitoringChemical', fn ($query) => $query->where('id', '!=', $recordId));
                                })
                                ->sum('quantity');

                            $tuvs = ChemicalQcTuv::where('purchase_order_issued_id', $item->id)->orderBy('id')->get();
                            $tuvTotal = $tuvs->sum('qty_qc_tuv');

                            $targetQty = $tuvTotal > 0 ? $tuvTotal : $item->qty_po;
                            $sisaBisaDiterima = max(0, $targetQty - $historyQty);
                            $autoQty = $sisaBisaDiterima > 0 ? $sisaBisaDiterima : null;
                            $tahapanValue = null;

                            if ($autoQty && $cat === 'Karung' && $tuvs->isNotEmpty()) {
                                $tahapanTerlibat = [];
                                $akumulasiTuvTarget = 0;
                                $totalAkumulasiSistem = $historyQty + $autoQty;

                                foreach ($tuvs as $index => $tuv) {
                                    $tahapStart = $akumulasiTuvTarget;
                                    $akumulasiTuvTarget += $tuv->qty_qc_tuv;

                                    if ($historyQty < $akumulasiTuvTarget && $totalAkumulasiSistem > $tahapStart) {
                                        preg_match('/\d+/', $tuv->tahapan_name, $matches);
                                        $angkaTahap = $matches[0] ?? ($index + 1);
                                        $tahapanTerlibat[] = $angkaTahap;
                                    }
                                }

                                if (! empty($tahapanTerlibat)) {
                                    $tahapanUnik = array_unique($tahapanTerlibat);
                                    $tahapanValue = 'TAHAP '.implode(',', $tahapanUnik).' TUV';
                                } else {
                                    $tahapanValue = 'TAHAPAN TUV PENUH / MELEBIHI TARGET';
                                }
                            }

                            $repeaterData[(string) Str::uuid()] = [
                                'purchase_order_issued_id' => $item->id,
                                'quantity' => $autoQty,
                                'tahapan' => $tahapanValue,
                                'is_qty_tolerance' => false,
                                'has_update_progress' => false,
                                'location_id' => null,
                                'tanggal_pengajuan_simala' => null,
                                'tanggal_pengambilan_sample' => null,
                                'tanggal_terbit_coa' => null,
                                'leadtime_coa' => null,
                                'notes' => null,
                            ];
                        }

                        $set('monitoringChemicalDetails', $repeaterData);
                    }),
                TextInput::make('do_number')
                    ->label('Nomor DO Sementara')
                    ->placeholder('Masukkan No. DO')
                    ->maxLength(15)
                    ->required(),

                DatePicker::make('received_date')
                    ->label('Tanggal Tiba')
                    ->native(false)
                    ->maxDate(now())
                    ->default(now())
                    ->required(),

                Select::make('received_by')
                    ->label('Diterima Oleh')
                    ->relationship('receivedBy', 'name')
                    ->default(Auth::id())
                    ->searchable()
                    ->preload()
                    ->required(),

                FileUpload::make('document_path')
                    ->label('Upload DO / AWB')
                    ->directory('monitoring-chemical-docs')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']),
            ]);
    }

    protected static function getDetailItemKedatanganSection(): Section
    {
        return Section::make('Detail Item Kedatangan')
            ->icon(Heroicon::OutlinedClipboardDocumentList)
            ->description('Tambahkan item yang diterima beserta tahapan TUV dan informasi QC.')
            ->schema([
                EmptyState::make('Belum Ada PO yang Dipilih')
                    ->description('Silakan pilih Kategori dan cari Nomor Purchase Order di form Informasi Dokumen untuk memunculkan item kedatangan.')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
                    ->visible(fn (Get $get, $record) => blank($get('search_po')) && $record === null)
                    ->columnSpanFull(),

                Repeater::make('monitoringChemicalDetails')
                    ->label('')
                    ->relationship('monitoringChemicalDetails')
                    ->collapsible()
                    ->visible(fn (Get $get, $record) => filled($get('search_po')) || $record !== null)
                    ->itemLabel(fn (array $state): ?string => $state['tahapan'] ?? 'Baris Item Baru')
                    ->schema([
                        Grid::make(2)->schema([
                            // Kolom 1 (Visual Kolom 2): Detail Item Kedatangan
                            Select::make('purchase_order_issued_id')
                                ->label('Informasi Item PO')
                                ->placeholder('Pilih Item PO...')
                                ->helperText('Pilih item dari daftar PO yang tersedia.')
                                ->prefixIcon('heroicon-o-shopping-bag')
                                ->options(fn (Get $get) => PurchaseOrderIssued::where('purchase_order_no', $get('../../search_po'))->get()->mapWithKeys(fn ($po) => [$po->id => "Item {$po->item_no} - {$po->description}"]))
                                ->native(false)
                                ->searchable()
                                ->required()
                                ->live()
                                ->columnSpanFull()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    if (! $state) {
                                        $set('quantity', null);
                                        $set('tahapan', null);

                                        return;
                                    }

                                    $recordId = $get('../../id');
                                    $cat = $get('../../material_category');
                                    $poItem = PurchaseOrderIssued::find($state);

                                    if (! $poItem) {
                                        return;
                                    }

                                    $historyQty = MonitoringChemicalDetail::where('purchase_order_issued_id', $state)
                                        ->when($recordId, function ($q) use ($recordId) {
                                            $q->whereHas('monitoringChemical', fn ($query) => $query->where('id', '!=', $recordId));
                                        })
                                        ->sum('quantity');

                                    $tuvs = ChemicalQcTuv::where('purchase_order_issued_id', $state)->orderBy('id')->get();
                                    $tuvTotal = $tuvs->sum('qty_qc_tuv');

                                    $targetQty = $tuvTotal > 0 ? $tuvTotal : $poItem->qty_po;
                                    $sisaBisaDiterima = max(0, $targetQty - $historyQty);
                                    $autoQty = $sisaBisaDiterima > 0 ? $sisaBisaDiterima : null;
                                    $tahapanValue = null;

                                    if ($autoQty && $cat === 'Karung' && $tuvs->isNotEmpty()) {
                                        $tahapanTerlibat = [];
                                        $akumulasiTuvTarget = 0;
                                        $totalAkumulasiSistem = $historyQty + $autoQty;

                                        foreach ($tuvs as $index => $tuv) {
                                            $tahapStart = $akumulasiTuvTarget;
                                            $akumulasiTuvTarget += $tuv->qty_qc_tuv;

                                            if ($historyQty < $akumulasiTuvTarget && $totalAkumulasiSistem > $tahapStart) {
                                                preg_match('/\d+/', $tuv->tahapan_name, $matches);
                                                $angkaTahap = $matches[0] ?? ($index + 1);
                                                $tahapanTerlibat[] = $angkaTahap;
                                            }
                                        }

                                        if (! empty($tahapanTerlibat)) {
                                            $tahapanUnik = array_unique($tahapanTerlibat);
                                            $tahapanValue = 'TAHAP '.implode(',', $tahapanUnik).' TUV';
                                        } else {
                                            $tahapanValue = 'TAHAPAN TUV PENUH / MELEBIHI TARGET';
                                        }
                                    }

                                    $set('quantity', $autoQty);
                                    $set('tahapan', $tahapanValue);
                                }),
                            Group::make()->schema([

                                Grid::make(2)->schema([
                                    TextInput::make('quantity')
                                        ->label('Qty Diterima')
                                        ->placeholder('0.00')
                                        ->helperText('Masukkan kuantitas fisik yang diterima.')
                                        ->numeric()
                                        ->required()
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->dehydrateStateUsing(fn ($state) => (float) str_replace(',', '', (string) $state))
                                        ->suffixIcon(Heroicon::OutlinedScale)
                                        ->live(onBlur: true)
                                        ->columnSpanFull()
                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                            $poId = $get('purchase_order_issued_id');
                                            $inputQty = (float) str_replace(',', '', (string) $state);

                                            if (! $poId || $inputQty <= 0) {
                                                if ($get('../../material_category') === 'Karung') {
                                                    $set('tahapan', null);
                                                }

                                                return;
                                            }

                                            $recordId = $get('../../id');
                                            $historyQty = MonitoringChemicalDetail::where('purchase_order_issued_id', $poId)
                                                ->when($recordId, function ($q) use ($recordId) {
                                                    $q->whereHas('monitoringChemical', fn ($query) => $query->where('id', '!=', $recordId));
                                                })
                                                ->sum('quantity');

                                            $totalAkumulasiSistem = $historyQty + $inputQty;
                                            $tuvs = ChemicalQcTuv::where('purchase_order_issued_id', $poId)->orderBy('id')->get();

                                            if ($get('../../material_category') === 'Karung' && $tuvs->isNotEmpty()) {
                                                $tahapanTerlibat = [];
                                                $akumulasiTuvTarget = 0;

                                                foreach ($tuvs as $index => $tuv) {
                                                    $tahapStart = $akumulasiTuvTarget;
                                                    $akumulasiTuvTarget += $tuv->qty_qc_tuv;

                                                    if ($historyQty < $akumulasiTuvTarget && $totalAkumulasiSistem > $tahapStart) {
                                                        preg_match('/\d+/', $tuv->tahapan_name, $matches);
                                                        $angkaTahap = $matches[0] ?? ($index + 1);
                                                        $tahapanTerlibat[] = $angkaTahap;
                                                    }
                                                }

                                                if (! empty($tahapanTerlibat)) {
                                                    $tahapanUnik = array_unique($tahapanTerlibat);
                                                    $set('tahapan', 'TAHAP '.implode(',', $tahapanUnik).' TUV');
                                                } else {
                                                    // Jika history sudah melebihi semua akumulasi target
                                                    $set('tahapan', 'TAHAPAN TUV PENUH / MELEBIHI TARGET');
                                                }
                                            }
                                        })
                                        ->rules([
                                            fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $poId = $get('purchase_order_issued_id');
                                                if (! $poId) {
                                                    return;
                                                }

                                                $inputQty = (float) str_replace(',', '', (string) $value);
                                                $poItem = PurchaseOrderIssued::find($poId);
                                                $totalPo = $poItem ? (float) $poItem->qty_po : 0;

                                                $tuvTotal = ChemicalQcTuv::where('purchase_order_issued_id', $poId)->sum('qty_qc_tuv');
                                                $baseMax = $tuvTotal > 0 ? (float) $tuvTotal : $totalPo;

                                                $isToleranceActive = (bool) ($get('is_qty_tolerance') ?? false);
                                                $finalMaxAllowed = $isToleranceActive ? ($baseMax + ($baseMax * 0.10)) : $baseMax;

                                                $recordId = $get('../../id');
                                                $historyQty = MonitoringChemicalDetail::where('purchase_order_issued_id', $poId)
                                                    ->when($recordId, function ($q) use ($recordId) {
                                                        $q->whereHas('monitoringChemical', fn ($query) => $query->where('id', '!=', $recordId));
                                                    })
                                                    ->sum('quantity');

                                                $sisaBisaDiterima = $finalMaxAllowed - $historyQty;

                                                // Tambahkan kuantitas dari repeater item lain di sesi ini yang memiliki poId sama
                                                preg_match('/monitoringChemicalDetails\.([^\.]+)\./', $attribute, $matches);
                                                $currentRowKey = $matches[1] ?? null;
                                                $detailsSekarang = $get('../../monitoringChemicalDetails') ?? [];
                                                $totalRepeaterLain = 0;
                                                foreach ($detailsSekarang as $key => $item) {
                                                    if ($key !== $currentRowKey && ($item['purchase_order_issued_id'] ?? null) == $poId) {
                                                        $totalRepeaterLain += (float) str_replace(',', '', (string) ($item['quantity'] ?? '0'));
                                                    }
                                                }

                                                $sisaBisaDiterima -= $totalRepeaterLain;

                                                if ($inputQty > $sisaBisaDiterima) {
                                                    $fmtSisa = rtrim(rtrim(number_format($sisaBisaDiterima, 2, '.', ','), '0'), '.');
                                                    $limitSource = $tuvTotal > 0 ? 'Target TUV' : 'PO';
                                                    if ($isToleranceActive) {
                                                        $fail("Overlimit! Dengan toleransi 10% {$limitSource}, sisa maksimal hanya {$fmtSisa}.");
                                                    } else {
                                                        $fail("Overlimit! Sisa kuota {$limitSource} hanya {$fmtSisa}. Aktifkan Toleransi jika perlu.");
                                                    }
                                                }
                                            },
                                        ]),

                                    Select::make('location_id')
                                        ->label('Lokasi Penyimpanan')
                                        ->placeholder('Pilih Gudang / Area')
                                        ->helperText('Pilih lokasi fisik penyimpanan.')
                                        ->prefixIcon('heroicon-o-map-pin')
                                        ->relationship('locationReceiving', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->columnSpanFull()
                                        ->required(),
                                ]),

                                TextInput::make('tahapan')
                                    ->label(fn (Get $get) => $get('../../material_category') === 'Karung' ? 'Tahapan (Auto)' : 'Tahapan')
                                    ->placeholder('Otomatis / Manual')
                                    ->helperText('Tahapan TUV berdasarkan alokasi.')
                                    ->prefixIcon('heroicon-o-flag')
                                    ->required()
                                    ->readOnly(fn (Get $get) => $get('../../material_category') === 'Karung'),
                            ]),

                            // Kolom 2 (Visual Kolom 3): Group Data TUV dan Toggle
                            Section::make('')
                                ->hiddenLabel()
                                ->schema([
                                    TextEntry::make('tuv_list')
                                        ->hiddenLabel()
                                        ->visible(fn (Get $get) => $get('../../material_category') === 'Karung')
                                        ->state(function (Get $get) {
                                            // Pancing pembaruan via hidden field
                                            $get('tuv_refresh_trigger');

                                            $poId = $get('purchase_order_issued_id');
                                            if (! $poId) {
                                                return new HtmlString('<span class="text-gray-500 italic text-sm">Pilih Informasi Item PO di Kolom 1.</span>');
                                            }

                                            $tuvs = ChemicalQcTuv::where('purchase_order_issued_id', $poId)->orderBy('id')->get();
                                            if ($tuvs->isEmpty()) {
                                                return new HtmlString('<span class="text-danger-600 font-medium text-sm">Belum ada target TUV. Silakan tambah.</span>');
                                            }

                                            $html = '<div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">';
                                            $html .= '<table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">';
                                            $html .= '<thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase font-medium text-gray-500 dark:text-gray-400">';
                                            $html .= '<tr>';
                                            $html .= '<th class="px-3 py-2">Tahapan</th>';
                                            $html .= '<th class="px-3 py-2 text-right">Target Qty</th>';
                                            $html .= '</tr>';
                                            $html .= '</thead>';
                                            $html .= '<tbody class="divide-y divide-gray-200 dark:divide-gray-700">';

                                            $tuvCount = $tuvs->count();
                                            if ($tuvCount > 3) {
                                                $html .= '<tr class="bg-gray-50 dark:bg-gray-800/30">';
                                                $html .= '<td class="px-3 py-2 text-gray-400 italic">...</td>';
                                                $html .= '<td class="px-3 py-2 text-right text-gray-400 italic">...</td>';
                                                $html .= '</tr>';

                                                $tuvsToDisplay = $tuvs->slice(-2);
                                            } else {
                                                $tuvsToDisplay = $tuvs;
                                            }

                                            foreach ($tuvsToDisplay as $tuv) {
                                                $qty = rtrim(rtrim(number_format($tuv->qty_qc_tuv, 2, ',', '.'), '0'), ',');
                                                $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">';
                                                $html .= "<td class=\"px-3 py-2 font-medium\">{$tuv->tahapan_name}</td>";
                                                $html .= "<td class=\"px-3 py-2 text-right text-primary-600 dark:text-primary-400 font-bold\">{$qty}</td>";
                                                $html .= '</tr>';
                                            }

                                            $totalQty = $tuvs->sum('qty_qc_tuv');
                                            $fmtTotal = rtrim(rtrim(number_format($totalQty, 2, ',', '.'), '0'), ',');
                                            $html .= '<tr class="bg-gray-100 dark:bg-gray-700 font-bold border-t border-gray-300 dark:border-gray-600">';
                                            $html .= '<td class="px-3 py-2">Total</td>';
                                            $html .= "<td class=\"px-3 py-2 text-right text-primary-700 dark:text-primary-300\">{$fmtTotal}</td>";
                                            $html .= '</tr>';

                                            $html .= '</tbody></table></div>';

                                            return new HtmlString($html);
                                        }),

                                    Actions::make([
                                        Action::make('addTuv')
                                            ->label('Tambah TUV')
                                            ->icon('heroicon-m-plus')
                                            ->size('sm')
                                            ->color('success')
                                            ->schema([
                                                Select::make('tahapan_name')
                                                    ->label('Nama Tahapan')
                                                    ->options(collect(range(1, 100))->mapWithKeys(fn ($i) => ["TAHAP $i TUV" => "TAHAP $i TUV"]))
                                                    ->searchable()
                                                    ->required(),
                                                TextInput::make('qty_qc_tuv')
                                                    ->label('Target Qty')
                                                    ->numeric()
                                                    ->required()
                                                    ->mask(RawJs::make('$money($input)'))
                                                    ->stripCharacters(','),
                                            ])
                                            ->action(function (array $data, Get $get, Set $set, Actions $component) {
                                                $poId = $get('purchase_order_issued_id');
                                                if (! $poId) {
                                                    return;
                                                }

                                                $tahapanName = $data['tahapan_name'];
                                                $exists = ChemicalQcTuv::where('purchase_order_issued_id', $poId)
                                                    ->where('tahapan_name', $tahapanName)
                                                    ->exists();

                                                if ($exists) {
                                                    Notification::make()
                                                        ->warning()
                                                        ->title('Peringatan')
                                                        ->body("{$tahapanName} sudah ada pada PO ini. Tidak dapat ditambahkan lagi.")
                                                        ->send();

                                                    return;
                                                }

                                                $inputQty = (float) str_replace(',', '', (string) $data['qty_qc_tuv']);

                                                // Validasi sisa PO
                                                $poItem = PurchaseOrderIssued::find($poId);
                                                $totalPo = $poItem ? (float) $poItem->qty_po : 0;
                                                $isToleranceActive = (bool) ($get('is_qty_tolerance') ?? false);
                                                $maxAllowedPo = $isToleranceActive ? ($totalPo + ($totalPo * 0.10)) : $totalPo;
                                                $existingTuvTotal = ChemicalQcTuv::where('purchase_order_issued_id', $poId)->sum('qty_qc_tuv');
                                                $sisaKuota = $maxAllowedPo - (float) $existingTuvTotal;

                                                if ($inputQty > $sisaKuota) {
                                                    $fmtSisa = rtrim(rtrim(number_format($sisaKuota, 2, '.', ','), '0'), '.');
                                                    Notification::make()->danger()->title('Kuota TUV Tidak Mencukupi')->body("Sisa kuota PO untuk TUV baru hanya {$fmtSisa}. Input dibatalkan.")->send();

                                                    return; // Silently fail UI side
                                                }

                                                ChemicalQcTuv::create([
                                                    'purchase_order_issued_id' => $poId,
                                                    'tahapan_name' => $data['tahapan_name'],
                                                    'qty_qc_tuv' => $inputQty,
                                                ]);
                                                Notification::make()->success()->title('Berhasil')->body('Target TUV ditambahkan.')->send();

                                                // Trigger re-render placeholder
                                                $component->getContainer()->getComponent('tuv_refresh_trigger')->state((string) Str::uuid());

                                                // Update tahapan manually if quantity is already filled
                                                $qtyCurrent = (float) str_replace(',', '', (string) $get('quantity'));
                                                if ($qtyCurrent > 0 && $get('../../material_category') === 'Karung') {
                                                    $recordId = $get('../../id');
                                                    $historyQty = MonitoringChemicalDetail::where('purchase_order_issued_id', $poId)
                                                        ->when($recordId, function ($q) use ($recordId) {
                                                            $q->whereHas('monitoringChemical', fn ($query) => $query->where('id', '!=', $recordId));
                                                        })
                                                        ->sum('quantity');

                                                    $totalAkumulasiSistem = $historyQty + $qtyCurrent;
                                                    $allTuvs = ChemicalQcTuv::where('purchase_order_issued_id', $poId)->orderBy('id')->get();

                                                    $tahapanTerlibat = [];
                                                    $akumulasiTuvTarget = 0;
                                                    foreach ($allTuvs as $idx => $t) {
                                                        $tahapStart = $akumulasiTuvTarget;
                                                        $akumulasiTuvTarget += $t->qty_qc_tuv;
                                                        if ($historyQty < $akumulasiTuvTarget && $totalAkumulasiSistem > $tahapStart) {
                                                            preg_match('/\d+/', $t->tahapan_name, $matches);
                                                            $angkaTahap = $matches[0] ?? ($idx + 1);
                                                            $tahapanTerlibat[] = $angkaTahap;
                                                        }
                                                    }
                                                    if (! empty($tahapanTerlibat)) {
                                                        $tahapanUnik = array_unique($tahapanTerlibat);
                                                        $set('tahapan', 'TAHAP '.implode(',', $tahapanUnik).' TUV');
                                                    } else {
                                                        $set('tahapan', 'TAHAPAN TUV PENUH / MELEBIHI TARGET');
                                                    }
                                                }
                                            }),

                                        Action::make('deleteTuv')
                                            ->label('Hapus')
                                            ->icon('heroicon-m-trash')
                                            ->size('sm')
                                            ->color('danger')
                                            ->mountUsing(function ($form, Actions $component) {
                                                $poId = $component->evaluate(fn (Get $get) => $get('purchase_order_issued_id'));
                                                $form->fill(['po_id' => $poId]);
                                            })
                                            ->schema([
                                                Hidden::make('po_id'),
                                                Select::make('tuv_id')
                                                    ->label('Pilih Tahapan yang Ingin Dihapus')
                                                    ->options(function (Get $get) {
                                                        $poId = $get('po_id');
                                                        if (! $poId) {
                                                            return [];
                                                        }

                                                        // Cek history quantity yang sudah diterima
                                                        $historyQty = MonitoringChemicalDetail::where('purchase_order_issued_id', $poId)->sum('quantity');

                                                        $tuvs = ChemicalQcTuv::where('purchase_order_issued_id', $poId)->orderBy('id')->get();
                                                        $options = [];
                                                        $akumulasi = 0;

                                                        foreach ($tuvs as $t) {
                                                            $tahapStart = $akumulasi;
                                                            $akumulasi += $t->qty_qc_tuv;

                                                            // TUV hanya bisa dihapus jika akumulasi penerimaan (historyQty)
                                                            // belum menyentuh batas awal tahap TUV ini.
                                                            if ($historyQty <= $tahapStart) {
                                                                $options[$t->id] = $t->tahapan_name.' ('.rtrim(rtrim(number_format($t->qty_qc_tuv, 2, ',', '.'), '0'), ',').')';
                                                            }
                                                        }

                                                        return collect($options);
                                                    })
                                                    ->searchable()
                                                    ->required()
                                                    ->helperText('Hanya TUV yang belum ada penerimaan (belum masuk Monitoring Chemical) yang bisa dihapus.'),
                                            ])
                                            ->action(function (array $data, Get $get, Set $set, Actions $component) {
                                                $tuv = ChemicalQcTuv::find($data['tuv_id']);
                                                if ($tuv) {
                                                    $tuvName = $tuv->tahapan_name;
                                                    $tuv->delete();
                                                    Notification::make()->success()->title('Berhasil')->body("{$tuvName} berhasil dihapus.")->send();

                                                    // Trigger re-render placeholder
                                                    $component->getContainer()->getComponent('tuv_refresh_trigger')->state((string) Str::uuid());

                                                    // Update tahapan manually if quantity is already filled
                                                    $poId = $get('purchase_order_issued_id');
                                                    $qtyCurrent = (float) str_replace(',', '', (string) $get('quantity'));
                                                    if ($qtyCurrent > 0 && $get('../../material_category') === 'Karung' && $poId) {
                                                        $recordId = $get('../../id');
                                                        $historyQty = MonitoringChemicalDetail::where('purchase_order_issued_id', $poId)
                                                            ->when($recordId, function ($q) use ($recordId) {
                                                                $q->whereHas('monitoringChemical', fn ($query) => $query->where('id', '!=', $recordId));
                                                            })
                                                            ->sum('quantity');

                                                        $totalAkumulasiSistem = $historyQty + $qtyCurrent;
                                                        $allTuvs = ChemicalQcTuv::where('purchase_order_issued_id', $poId)->orderBy('id')->get();

                                                        $tahapanTerlibat = [];
                                                        $akumulasiTuvTarget = 0;
                                                        foreach ($allTuvs as $idx => $t) {
                                                            $tahapStart = $akumulasiTuvTarget;
                                                            $akumulasiTuvTarget += $t->qty_qc_tuv;
                                                            if ($historyQty < $akumulasiTuvTarget && $totalAkumulasiSistem > $tahapStart) {
                                                                preg_match('/\d+/', $t->tahapan_name, $matches);
                                                                $angkaTahap = $matches[0] ?? ($idx + 1);
                                                                $tahapanTerlibat[] = $angkaTahap;
                                                            }
                                                        }
                                                        if (! empty($tahapanTerlibat)) {
                                                            $tahapanUnik = array_unique($tahapanTerlibat);
                                                            $set('tahapan', 'TAHAP '.implode(',', $tahapanUnik).' TUV');
                                                        } else {
                                                            $set('tahapan', $allTuvs->isEmpty() ? null : 'TAHAPAN TUV PENUH / MELEBIHI TARGET');
                                                        }
                                                    }
                                                }
                                            }),

                                    ])->visible(fn (Get $get) => $get('../../material_category') === 'Karung'),

                                    Hidden::make('tuv_refresh_trigger')->live(),

                                    Grid::make(2)->schema([
                                        Toggle::make('is_qty_tolerance')
                                            ->label('Toleransi Qty Aktif?')
                                            ->helperText('Izinkan kelebihan Qty hingga 10%')
                                            ->inline(false)
                                            ->default(false),

                                        Toggle::make('has_update_progress')
                                            ->label('Update Progress QC?')
                                            ->helperText('Buka form pengisian Simala & COA')
                                            ->inline(false)
                                            ->live()
                                            ->default(false),
                                    ]),
                                ]),
                            Grid::make(['default' => 1, 'sm' => 2, 'xl' => 2])
                                ->schema([
                                    DatePicker::make('tanggal_pengajuan_simala')
                                        ->label('Pengajuan Simala')
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->placeholder('Pilih Tanggal')
                                        ->prefixIcon('heroicon-o-calendar-days')
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, Get $get) {
                                            $tglPengajuan = $get('tanggal_pengajuan_simala');
                                            $tglTerbit = $get('tanggal_terbit_coa');

                                            if ($tglPengajuan && $tglTerbit) {
                                                $diff = \Carbon\Carbon::parse($tglPengajuan)->diffInDays(\Carbon\Carbon::parse($tglTerbit));
                                                $set('leadtime_coa', $diff);
                                            } else {
                                                $set('leadtime_coa', null);
                                            }
                                        }),

                                    DatePicker::make('tanggal_pengambilan_sample')
                                        ->label('Pengambilan Sample')
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->placeholder('Pilih Tanggal')
                                        ->prefixIcon('heroicon-o-beaker'),

                                    DatePicker::make('tanggal_terbit_coa')
                                        ->label('Terbit COA')
                                        ->native(false)
                                        ->displayFormat('d/m/Y')
                                        ->placeholder('Pilih Tanggal')
                                        ->prefixIcon('heroicon-o-document-check')
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, Get $get) {
                                            $tglPengajuan = $get('tanggal_pengajuan_simala');
                                            $tglTerbit = $get('tanggal_terbit_coa');

                                            if ($tglPengajuan && $tglTerbit) {
                                                $diff = \Carbon\Carbon::parse($tglPengajuan)->diffInDays(\Carbon\Carbon::parse($tglTerbit));
                                                $set('leadtime_coa', $diff);
                                            } else {
                                                $set('leadtime_coa', null);
                                            }
                                        }),

                                    TextInput::make('leadtime_coa')
                                        ->label('Leadtime COA')
                                        ->numeric()
                                        ->readOnly()
                                        ->placeholder('Auto-hitung')
                                        ->suffix('Hari')
                                        ->prefixIcon('heroicon-o-clock')
                                        ->extraInputAttributes(['class' => 'font-bold text-primary-600']),
                                ])->columnSpanFull()->visible(fn (Get $get) => $get('has_update_progress')),
                        ]),

                        Textarea::make('notes')
                            ->label('Keterangan Tambahan')
                            ->placeholder('Tambahkan catatan atau observasi terkait item ini...')
                            ->helperText('Opsional: Keterangan mengenai kondisi fisik, masalah QC, atau lainnya.')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data) {
                        if (! empty($data['tanggal_pengajuan_simala']) && ! empty($data['tanggal_terbit_coa'])) {
                            $diff = Carbon::parse($data['tanggal_pengajuan_simala'])->diffInDays(Carbon::parse($data['tanggal_terbit_coa']));
                            $data['leadtime_coa'] = $diff;
                        }

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data) {
                        if (! empty($data['tanggal_pengajuan_simala']) && ! empty($data['tanggal_terbit_coa'])) {
                            $diff = Carbon::parse($data['tanggal_pengajuan_simala'])->diffInDays(Carbon::parse($data['tanggal_terbit_coa']));
                            $data['leadtime_coa'] = $diff;
                        }

                        return $data;
                    })
                    ->columns(1)
                    ->defaultItems(0)
                    ->addActionLabel('Tambah Item Manual'),
            ]);
    }

    protected static function getSummaryPoSection(): Section
    {
        return Section::make('Summary PO (Keseluruhan Item)')
            ->icon(Heroicon::OutlinedTableCells)
            ->visible(function (Get $get, $record) {
                return filled($get('search_po')) || $record !== null;
            })
            ->schema([
                TextEntry::make('summary_table')
                    ->hiddenLabel()
                    ->extraAttributes(fn (Get $get) => [
                        'wire:key' => 'summary-po-'.mt_rand(),
                    ])
                    ->getStateUsing(function (Get $get, $record) {
                        $poNo = $get('search_po');
                        if (! $poNo) {
                            return new HtmlString('<p class="text-sm text-gray-500 italic">Data PO tidak ditemukan.</p>');
                        }

                        $allItems = PurchaseOrderIssued::where('purchase_order_no', $poNo)
                            ->orderBy('item_no')
                            ->get();

                        if ($allItems->isEmpty()) {
                            return new HtmlString('');
                        }

                        $recordId = $record ? $record->id : null;

                        // Hitung akumulasi dari repeater per item
                        $detailsSekarang = $get('monitoringChemicalDetails') ?? [];
                        $qtySaatIniPerItem = [];
                        foreach ($detailsSekarang as $detail) {
                            $itemId = $detail['purchase_order_issued_id'] ?? null;
                            if ($itemId) {
                                $qty = (float) str_replace(',', '', (string) ($detail['quantity'] ?? '0'));
                                if (! isset($qtySaatIniPerItem[$itemId])) {
                                    $qtySaatIniPerItem[$itemId] = 0;
                                }
                                $qtySaatIniPerItem[$itemId] += $qty;
                            }
                        }

                        $html = '<div class="fi-ta-content divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-gray-700 dark:bg-gray-900 dark:ring-white/10">';
                        $html .= '<div class="overflow-x-auto">';
                        $html .= '<table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">';
                        $html .= '<thead class="bg-gray-50 dark:bg-white/5">
                                    <tr>
                                        <th class="px-3 py-2.5 text-left text-sm font-semibold text-gray-950 dark:text-white whitespace-nowrap">Item No</th>
                                        <th class="px-3 py-2.5 text-left text-sm font-semibold text-gray-950 dark:text-white">Deskripsi</th>
                                        <th class="px-3 py-2.5 text-right text-sm font-semibold text-gray-950 dark:text-white whitespace-nowrap">Qty PO</th>
                                        <th class="px-3 py-2.5 text-right text-sm font-semibold text-gray-950 dark:text-white whitespace-nowrap">Qty Riwayat</th>
                                        <th class="px-3 py-2.5 text-right text-sm font-semibold text-gray-950 dark:text-white whitespace-nowrap">Qty Masuk Saat Ini</th>
                                        <th class="px-3 py-2.5 text-right text-sm font-semibold text-gray-950 dark:text-white whitespace-nowrap">Sisa Qty</th>
                                    </tr>
                                </thead>';
                        $html .= '<tbody class="divide-y divide-gray-200 dark:divide-gray-700">';

                        foreach ($allItems as $item) {
                            $totalDiterimaRiwayat = MonitoringChemicalDetail::whereHas('monitoringChemical', function ($q) use ($recordId) {
                                $q->when($recordId, fn ($query) => $query->where('id', '!=', $recordId));
                            })->where('purchase_order_issued_id', $item->id)->sum('quantity');

                            $qtySaatIni = $qtySaatIniPerItem[$item->id] ?? 0;
                            $totalKeseluruhan = $totalDiterimaRiwayat + $qtySaatIni;
                            $qtyPoFloat = (float) $item->qty_po;
                            $sisa = $qtyPoFloat - $totalKeseluruhan;

                            $colorClass = '';
                            $badgeClass = '';
                            if ($sisa <= 0) {
                                $colorClass = 'text-success-600 dark:text-success-400 whitespace-nowrap';
                                $badgeClass = 'inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-xs font-medium tracking-tight rounded-md text-success-700 bg-success-50 ring-1 ring-inset ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/30 whitespace-nowrap';
                            } elseif ($sisa < 0) {
                                $colorClass = 'text-danger-600 dark:text-danger-400 font-bold whitespace-nowrap';
                                $badgeClass = 'inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-xs font-medium tracking-tight rounded-md text-danger-700 bg-danger-50 ring-1 ring-inset ring-danger-600/10 dark:bg-danger-400/10 dark:text-danger-400 dark:ring-danger-400/20 whitespace-nowrap';
                            } else {
                                $colorClass = 'text-gray-500 dark:text-gray-400 whitespace-nowrap';
                            }

                            $fmtPo = rtrim(rtrim(number_format($qtyPoFloat, 2, ',', '.'), '0'), ',');
                            $fmtTerimaRiwayat = rtrim(rtrim(number_format($totalDiterimaRiwayat, 2, ',', '.'), '0'), ',');
                            $fmtSaatIni = rtrim(rtrim(number_format($qtySaatIni, 2, ',', '.'), '0'), ',');
                            $fmtSisa = rtrim(rtrim(number_format($sisa, 2, ',', '.'), '0'), ',');

                            $saatIniColor = $qtySaatIni > 0 ? 'text-primary-600 dark:text-primary-400 font-bold' : 'text-gray-400';
                            $shortDescription = Str::limit($item->description, 20, '...');
                            $escapedDescription = htmlspecialchars($item->description, ENT_QUOTES, 'UTF-8');

                            $html .= "<tr class='hover:bg-gray-50 dark:hover:bg-white/5 transition duration-75'>";
                            $html .= "<td class='px-3 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap'>{$item->item_no}</td>";
                            $html .= "<td class='px-3 py-3 text-sm text-gray-950 dark:text-white font-medium'>";
                            if (strlen($item->description) > 20) {
                                $html .= "<span x-data x-tooltip=\"'{$escapedDescription}'\" class='cursor-help border-b border-dashed border-gray-300 dark:border-gray-600'>{$shortDescription}</span>";
                            } else {
                                $html .= "<span>{$item->description}</span>";
                            }
                            $html .= '</td>';
                            $html .= "<td class='px-3 py-3 text-sm text-right text-gray-500 dark:text-gray-400 whitespace-nowrap'>{$fmtPo} {$item->uoi}</td>";
                            $html .= "<td class='px-3 py-3 text-sm text-right font-semibold text-success-600 dark:text-success-400 whitespace-nowrap'>{$fmtTerimaRiwayat} {$item->uoi}</td>";
                            $html .= "<td class='px-3 py-3 text-sm text-right {$saatIniColor} whitespace-nowrap'>+ {$fmtSaatIni} {$item->uoi}</td>";
                            $html .= "<td class='px-3 py-3 text-sm text-right whitespace-nowrap'>";
                            if ($badgeClass !== '') {
                                $html .= "<span class='{$badgeClass}'>{$fmtSisa} {$item->uoi}</span>";
                            } else {
                                $html .= "<span class='{$colorClass}'>{$fmtSisa} {$item->uoi}</span>";
                            }
                            $html .= '</td></tr>';
                        }

                        $html .= '</tbody></table></div></div>';

                        return new HtmlString($html);
                    }),
            ]);
    }
}
