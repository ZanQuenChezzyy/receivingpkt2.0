<?php

namespace App\Filament\Resources\DeliveryOrderReceipts\Schemas;

use App\Models\DeliveryOrderReceiptDetail;
use App\Models\PurchaseOrderIssued;
use Carbon\Carbon;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class DeliveryOrderReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Informasi Surat Jalan & PO')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('document_code')
                                    ->label('Kode Dokumen')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-m-qr-code')
                                    ->copyable()
                                    ->columnSpanFull(),

                                TextEntry::make('deliveryOrderReceiptDetails.0.purchaseOrderIssued.purchase_order_no')
                                    ->label('Nomor Purchase Order')
                                    ->icon('heroicon-m-shopping-cart')
                                    ->placeholder('Tidak ada PO terkait')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('delivery_oder_no')
                                    ->label('No. Surat Jalan (DO)')
                                    ->icon('heroicon-m-truck')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('received_date')
                                    ->label('Tanggal Kedatangan')
                                    ->icon('heroicon-m-calendar-days')
                                    ->date('d F Y'),

                                TextEntry::make('source_type')
                                    ->label('Tipe Material')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Bahan Baku NPK' => 'success',
                                        'Chemical/Karung' => 'warning',
                                        'Sparepart' => 'info',
                                        default => 'gray',
                                    }),
                            ]),
                        ]),

                    Section::make('Dokumen GRS & RDTV Terkait')
                        ->icon(Heroicon::OutlinedDocumentCurrencyDollar)
                        ->schema([
                            RepeatableEntry::make('grsRdtvItems')
                                ->hiddenLabel()
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextEntry::make('grsRdtv.category')
                                            ->label('Kategori')
                                            ->badge()
                                            ->color(fn ($state) => match ($state) {
                                                'GRS' => 'success',
                                                'RDTV' => 'warning',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('created_at')
                                            ->label('Waktu Unggah')
                                            ->dateTime(),
                                        TextEntry::make('file_path')
                                            ->label('File Dokumen')
                                            ->formatStateUsing(fn () => 'Lihat Dokumen')
                                            ->url(fn ($record) => Storage::url($record->file_path))
                                            ->openUrlInNewTab()
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->color('primary'),
                                        TextEntry::make('grsRdtv.createdBy.name')
                                            ->label('Diunggah Oleh'),
                                    ]),
                                ])
                                ->visible(fn ($record) => $record->grsRdtvItems()->exists()),

                            TextEntry::make('no_docs')
                                ->hiddenLabel()
                                ->placeholder('Belum ada dokumen GRS/RDTV yang ditautkan ke DO ini.')
                                ->visible(fn ($record) => ! $record->grsRdtvItems()->exists()),
                        ]),

                    // 📦 TABEL DETAIL MATERIAL (LENGKAP)
                    Section::make('Daftar Material Diterima')
                        ->icon('heroicon-o-cube')
                        ->schema([
                            RepeatableEntry::make('deliveryOrderReceiptDetails')
                                ->hiddenLabel()
                                ->schema([
                                    // Baris 1: Informasi Utama Material
                                    Grid::make(4)->schema([
                                        TextEntry::make('material_code')
                                            ->label('Kode Material')
                                            ->placeholder('None')
                                            ->weight(FontWeight::SemiBold)
                                            ->color('gray'),

                                        TextEntry::make('description')
                                            ->label('Deskripsi')
                                            ->columnSpan(2),

                                        TextEntry::make('quantity')
                                            ->label('Qty Aktual')
                                            ->weight(FontWeight::Bold)
                                            ->color('success')
                                            ->suffix(fn ($record) => " {$record->uoi}")
                                            ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.')),
                                    ]),

                                    // Baris 2: Lokasi & Status Toleransi
                                    Grid::make(2)->schema([
                                        TextEntry::make('locationReceiving.name')
                                            ->label('Lokasi Penyimpanan')
                                            ->icon('heroicon-m-map-pin')
                                            ->badge()
                                            ->color('info')
                                            ->placeholder('Belum diatur'),

                                        TextEntry::make('is_qty_tolerance')
                                            ->label('Status Toleransi')
                                            ->badge()
                                            ->color(fn ($state) => $state ? 'danger' : 'success')
                                            ->icon(fn ($state) => $state ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                                            ->formatStateUsing(function ($state, $record) {
                                                // 1. Jika False (Normal), tampilkan teks biasa
                                                if (! $state) {
                                                    return 'Normal';
                                                }

                                                // 2. Jika True (Toleransi), hitung jumlah kelebihannya
                                                $poId = $record->purchase_order_issued_id;
                                                $itemNo = $record->item_no;

                                                if ($poId && $itemNo) {
                                                    // Ambil target Qty dari PO
                                                    $poItem = PurchaseOrderIssued::find($poId);
                                                    $qtyPo = $poItem ? (float) $poItem->qty_po : 0;

                                                    // Hitung total Qty yang sudah masuk ke database untuk item ini
                                                    $totalReceived = DeliveryOrderReceiptDetail::where('purchase_order_issued_id', $poId)
                                                        ->where('item_no', $itemNo)
                                                        ->sum('quantity');

                                                    // Kalkulasi selisih (kelebihan)
                                                    $lebihan = $totalReceived - $qtyPo;

                                                    // Jika benar-benar berlebih, tampilkan angkanya
                                                    if ($lebihan > 0) {
                                                        // Format angka agar rapi (misal: +1.500 EA)
                                                        $fmtLebihan = number_format($lebihan, 0, ',', '.');

                                                        return "Toleransi (+{$fmtLebihan} {$record->uoi})";
                                                    }
                                                }

                                                return 'Toleransi Aktif';
                                            }),
                                    ]),

                                    // Baris 3: Data Teknis Tambahan (SAP / Master Data)
                                    Grid::make(5)->schema([
                                        TextEntry::make('item_no')
                                            ->label('Item No.')
                                            ->size(TextSize::Small)
                                            ->color('gray'),

                                        TextEntry::make('material_type')
                                            ->label('Mat. Type')
                                            ->size(TextSize::Small)
                                            ->color('gray'),

                                        TextEntry::make('mrp_type')
                                            ->label('MRP Type')
                                            ->size(TextSize::Small)
                                            ->color('gray'),

                                        TextEntry::make('aac')
                                            ->label('AAC')
                                            ->size(TextSize::Small)
                                            ->color('gray'),

                                        TextEntry::make('abc_indicator')
                                            ->label('ABC Ind.')
                                            ->size(TextSize::Small)
                                            ->color('gray'),
                                    ])
                                        ->extraAttributes(['style' => 'border-top: 1px dashed #e5e7eb; padding-top: 12px; margin-top: 12px;']), // Garis pemisah halus

                                    // Baris 4: Informasi Finansial & Pengaju
                                    Grid::make(3)->schema([
                                        TextEntry::make('requisitioner')
                                            ->label('Requisitioner')
                                            ->icon('heroicon-m-user-circle')
                                            ->size(TextSize::Small)
                                            ->color('gray'),

                                        // Menarik Net Price dari hasil konversi Local Currency (IDR)
                                        TextEntry::make('net_price_lc')
                                            ->label('Net Price (IDR)')
                                            ->size(TextSize::Small)
                                            ->color('warning') // Diberi warna berbeda agar menonjol
                                            ->money('IDR', locale: 'id')
                                            ->getStateUsing(function ($record) {
                                                $po = $record->purchaseOrderIssued;
                                                if ($po && $po->qty_po > 0) {
                                                    return (float) $po->total_amount_in_lc / (float) $po->qty_po;
                                                }

                                                return $po ? $po->net_price : 0;
                                            })
                                            ->placeholder('Harga tidak tersedia'),

                                        TextEntry::make('total_amount_snapshot')
                                            ->label('Estimasi Total Harga')
                                            ->size(TextSize::Small)
                                            ->color('gray')
                                            ->money('IDR', locale: 'id'),
                                    ]),
                                ])
                                ->columns(1), // Memastikan setiap kelompok material (card) turun ke baris baru
                        ]),
                ]),

                Group::make([
                    Section::make('Riwayat Pengambilan Barang (MIR)')
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->schema([
                            TextEntry::make('riwayat_pengambilan')
                                ->hiddenLabel()
                                ->html()
                                ->getStateUsing(function ($record) {
                                    $details = $record->deliveryOrderReceiptDetails()->with('materialIssueDetails.materialIssue')->get();
                                    $mirs = [];
                                    foreach ($details as $d) {
                                        foreach ($d->materialIssueDetails as $mid) {
                                            $mirs[] = [
                                                'mir_number' => $mid->materialIssue->mir_number,
                                                'tanggal' => $mid->materialIssue->tanggal,
                                                'peminta' => $mid->materialIssue->diminta_oleh,
                                                'item' => $d->description,
                                                'qty' => number_format((float) $mid->diserahkan, 0, ',', '.'),
                                                'stage' => $mid->stage_when_issued,
                                                'uoi' => $d->uoi,
                                            ];
                                        }
                                    }
                                    if (empty($mirs)) {
                                        return '<p class="text-sm text-gray-500 dark:text-gray-400">Belum ada barang yang diambil.</p>';
                                    }

                                    $html = '<div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10"><table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-white/5 dark:text-gray-300">
                                            <tr>
                                                <th class="px-4 py-3">No. MIR</th>
                                                <th class="px-4 py-3">Tanggal</th>
                                                <th class="px-4 py-3">Peminta</th>
                                                <th class="px-4 py-3">Item</th>
                                                <th class="px-4 py-3">Qty Diambil</th>
                                                <th class="px-4 py-3">Stage Saat Diambil</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                    foreach ($mirs as $m) {
                                        $stageLabel = $m['stage'] ?? 'Default';
                                        $html .= '<tr class="bg-white border-b dark:bg-transparent dark:border-white/5">
                                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">'.$m['mir_number'].'</td>
                                            <td class="px-4 py-3">'.Carbon::parse($m['tanggal'])->format('d M Y').'</td>
                                            <td class="px-4 py-3">'.$m['peminta'].'</td>
                                            <td class="px-4 py-3">'.$m['item'].'</td>
                                            <td class="px-4 py-3 text-success-600 dark:text-success-400 font-bold">'.$m['qty'].' '.$m['uoi'].'</td>
                                            <td class="px-4 py-3"><span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-md dark:bg-blue-900/30 dark:text-blue-300">'.$stageLabel.'</span></td>
                                        </tr>';
                                    }
                                    $html .= '</tbody></table></div>';

                                    return $html;
                                }),
                        ]),

                    Section::make('Status Progress')
                        ->icon('heroicon-o-chart-bar')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('status')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'Pending' => 'warning',
                                            'Diterima' => 'success',
                                            default => 'primary',
                                        }),

                                    TextEntry::make('delay_reason')
                                        ->label(fn ($record) => $record->status === 'Pending' ? 'Alasan Pending' : 'Riwayat Pending (Alasan)')
                                        ->icon('heroicon-m-exclamation-triangle')
                                        ->color(fn ($record) => $record->status === 'Pending' ? 'danger' : 'gray')
                                        ->weight(FontWeight::Bold)
                                        ->visible(fn ($record) => !empty($record->delay_reason)),

                                    TextEntry::make('pending_date')
                                        ->label('Tanggal Mulai Pending')
                                        ->dateTime('d M Y, H:i')
                                        ->color('gray')
                                        ->icon('heroicon-m-calendar')
                                        ->visible(fn ($record) => !empty($record->pending_date)),

                                    TextEntry::make('pending_resolved_date')
                                        ->label('Tanggal Selesai Pending')
                                        ->dateTime('d M Y, H:i')
                                        ->color('success')
                                        ->icon('heroicon-m-check-circle')
                                        ->visible(fn ($record) => !empty($record->pending_resolved_date)),

                                    TextEntry::make('delay_notes')
                                        ->label('Catatan Pending')
                                        ->color('gray')
                                        ->visible(fn ($record) => !empty($record->delay_notes)),

                                    TextEntry::make('stage')
                                        ->label(function ($record) {
                                            // Ubah label secara dinamis berdasarkan isi kolom stage
                                            if (str_contains(strtoupper($record->stage ?? ''), 'DOF')) {
                                                return 'Sumber Dokumen';
                                            } elseif (str_contains(strtoupper($record->stage ?? ''), 'TERMIN')) {
                                                return 'Termin Penerimaan';
                                            }

                                            return 'Tahapan Penerimaan';
                                        })
                                        ->badge()
                                        ->color(function ($record) {
                                            // Ubah warna secara dinamis
                                            if (str_contains(strtoupper($record->stage ?? ''), 'DOF')) {
                                                return 'info'; // Biru untuk DOF
                                            } elseif (str_contains(strtoupper($record->stage ?? ''), 'TERMIN')) {
                                                return 'warning'; // Kuning/Oranye untuk Termin
                                            }

                                            return 'success'; // Hijau untuk Default
                                        })
                                        ->icon(function ($record) {
                                            // Ubah ikon secara dinamis
                                            if (str_contains(strtoupper($record->stage ?? ''), 'DOF')) {
                                                return 'heroicon-m-document-duplicate';
                                            } elseif (str_contains(strtoupper($record->stage ?? ''), 'TERMIN')) {
                                                return 'heroicon-m-chart-pie';
                                            }

                                            return 'heroicon-m-tag';
                                        })
                                        ->formatStateUsing(function ($state, $record) {
                                            // Jika tidak ada isi (null atau kosong), ini pasti mode Default tanpa tahapan
                                            if (empty($state)) {
                                                return 'Tidak Ada Tahapan (Default)';
                                            }

                                            $upperState = strtoupper($state);

                                            // Jika mode SURAT DOF
                                            if (str_contains($upperState, 'DOF')) {
                                                return 'Dari Surat DOF ('.$state.')';
                                            }

                                            // Jika mode TERMIN
                                            if (str_contains($upperState, 'TERMIN')) {
                                                // Ambil persentase dari kolom termin_percentage jika ada,
                                                // atau hitung persentase jika tidak ada kolom tersebut
                                                $percentage = $record->termin_percentage ?? null;

                                                if ($percentage) {
                                                    return "{$state}: {$percentage}%";
                                                } else {
                                                    return "{$state}"; // Fallback jika tidak ada data persentase
                                                }
                                            }

                                            // Jika mode Default TAPI ada isi tahapannya (Misal: "Tahap 1")
                                            return $state;
                                        })
                                        // Placeholder digunakan ketika formatStateUsing mengembalikan string kosong
                                        // (namun logika di atas sudah menangani empty state, ini hanya lapisan keamanan ganda)
                                        ->placeholder('Tidak Ada Tahapan'),

                                    TextEntry::make('post_103')
                                        ->label('Status Post 103 (SAP)')
                                        ->placeholder('Belum Post 103')
                                        ->formatStateUsing(fn ($state) => $state ? 'Sudah di-Post' : 'Belum Post')
                                        ->icon(fn ($state) => $state ? 'heroicon-m-check-circle' : 'heroicon-m-clock')
                                        ->color(fn ($state) => $state ? 'success' : 'gray')
                                        ->weight(fn ($state) => $state ? FontWeight::Bold : FontWeight::Normal),

                                    TextEntry::make('post_103_date')
                                        ->label('Waktu Post 103')
                                        ->getStateUsing(fn ($record) => $record->post_103)
                                        ->dateTime('l, d F Y')
                                        ->visible(fn ($record) => $record->post_103 !== null)
                                        ->color('gray'),
                                ]),
                        ]),

                    Section::make('Timeline & Riwayat Pemeriksaan')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('dikirim_ke_istek')
                                        ->label('Dikirim ke ISTEK')
                                        ->getStateUsing(fn ($record) => $record->qcHistories()->where('status', 'Kirim')->latest()->first()?->created_at)
                                        ->dateTime('d F Y, H:i')
                                        ->placeholder('Belum Dikirim')
                                        ->icon('heroicon-m-paper-airplane')
                                        ->color(fn ($state) => $state ? 'info' : 'gray'),

                                    TextEntry::make('kembali_dari_istek')
                                        ->label('Kembali dari ISTEK')
                                        ->getStateUsing(fn ($record) => $record->qcHistories()->where('status', 'Kembali')->latest()->first()?->created_at)
                                        ->dateTime('d F Y, H:i')
                                        ->placeholder('Belum Kembali')
                                        ->icon('heroicon-m-arrow-uturn-left')
                                        ->color(fn ($state) => $state ? 'success' : 'gray'),

                                    TextEntry::make('grs_rdtv_date')
                                        ->label('Tanggal GRS / RDTV')
                                        ->getStateUsing(fn ($record) => $record->grsRdtvItems()->latest()->first()?->grsRdtv?->transaction_date)
                                        ->date('d F Y')
                                        ->placeholder('Belum Diproses')
                                        ->icon('heroicon-m-document-check')
                                        ->color(fn ($state) => $state ? 'primary' : 'gray'),
                                ]),
                        ])
                        ->collapsible(),

                    Section::make('Informasi Petugas')
                        ->icon('heroicon-o-users')
                        ->schema([
                            TextEntry::make('receivedBy.name')
                                ->label('Diterima Oleh')
                                ->icon('heroicon-m-user')
                                ->weight(FontWeight::Medium),

                            TextEntry::make('createdBy.name')
                                ->label('Dibuat Oleh')
                                ->icon('heroicon-m-computer-desktop')
                                ->placeholder('Tidak diketahui'),

                            TextEntry::make('created_at')
                                ->label('Dibuat Pada')
                                ->dateTime('d M Y H:i')
                                ->color('gray'),

                            TextEntry::make('updated_at')
                                ->label('Terakhir Diubah')
                                ->since()
                                ->color('gray'),
                        ])->columns(2),

                    Section::make('Ringkasan Dokumen')
                        ->icon(Heroicon::OutlinedClipboardDocumentList)
                        ->schema([
                            Grid::make(2)->schema([

                                // 1. Total Item Unik
                                TextEntry::make('total_item_summary')
                                    ->label('Total Item')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->getStateUsing(function ($record) {
                                        $total = $record->deliveryOrderReceiptDetails->count();

                                        return "{$total} Item";
                                    }),

                                // 2. Agregasi AAC (Menghitung jumlah tiap kategori AAC)
                                TextEntry::make('aac_summary')
                                    ->label('Account Assignment')
                                    ->html() // Izinkan HTML agar bisa diformat rapi ke bawah
                                    ->getStateUsing(function ($record) {
                                        $details = $record->deliveryOrderReceiptDetails;
                                        if ($details->isEmpty()) {
                                            return '-';
                                        }

                                        // Kumpulkan, Grouping, dan Hitung jumlahnya
                                        $aacCounts = $details->groupBy('aac')
                                            ->map(fn ($group) => $group->count())
                                            ->sortDesc(); // Urutkan dari yang terbanyak

                                        // Format menjadi list HTML
                                        $output = [];
                                        foreach ($aacCounts as $aac => $count) {
                                            $label = $aac ?: 'Tidak Ada'; // Jaga-jaga jika ada AAC yang kosong
                                            $output[] = "<span class='text-sm text-gray-400'>{$label}: {$count}</span>";
                                        }

                                        return implode('<br>', $output);
                                    }),

                                // 3. Agregasi ABC Indicator
                                TextEntry::make('abc_summary')
                                    ->label('ABC Indicator')
                                    ->html()
                                    ->getStateUsing(function ($record) {
                                        $details = $record->deliveryOrderReceiptDetails;
                                        if ($details->isEmpty()) {
                                            return '-';
                                        }

                                        $abcCounts = $details->groupBy('abc_indicator')
                                            ->map(fn ($group) => $group->count())
                                            ->sortDesc();

                                        $output = [];
                                        foreach ($abcCounts as $abc => $count) {
                                            // Jika nilainya null atau string kosong, lewati (jangan dimasukkan)
                                            if (empty($abc)) {
                                                continue;
                                            }

                                            $output[] = "<span class='text-sm text-gray-400'>{$abc}: {$count}</span>";
                                        }

                                        // Jika setelah difilter ternyata tidak ada data sama sekali, tampilkan strip
                                        if (empty($output)) {
                                            return "<span class='text-sm text-gray-400'>Tidak ada data</span>";
                                        }

                                        return implode('<br>', $output);
                                    }),

                                // 5. Agregasi Nilai per MRP Type (Sum Amount)
                                TextEntry::make('mrp_type_summary')
                                    ->label('Nilai per MRP Type')
                                    ->html()
                                    ->getStateUsing(function ($record) {
                                        $details = $record->deliveryOrderReceiptDetails;

                                        if ($details->isEmpty()) {
                                            // Gunakan text-gray-500 untuk light mode, dark:text-gray-400 untuk dark mode
                                            return "<span class='text-sm text-gray-500 dark:text-gray-400'>-</span>";
                                        }

                                        // Mengelompokkan berdasarkan mrp_type lalu MENJUMLAHKAN total_amount_snapshot
                                        $mrpSums = $details->groupBy('mrp_type')
                                            ->map(fn ($group) => $group->sum('total_amount_snapshot'))
                                            ->sortDesc(); // Urutkan dari nilai Rupiah terbesar

                                        $output = [];
                                        foreach ($mrpSums as $mrp => $total) {
                                            // Lewati jika MRP Type kosong
                                            if (empty($mrp)) {
                                                continue;
                                            }

                                            // Format angka menjadi Rupiah (misal: Rp 1.500.000)
                                            $fmtTotal = 'Rp '.number_format($total, 2, ',', '.');

                                            // Set warna teks standar menjadi abu-abu adaptif, dan warna Rupiah persis mengikuti ->color('success') Filament
                                            $output[] = "<span class='text-sm text-gray-500 dark:text-gray-400'>{$mrp}: <strong class='text-success-600 dark:text-success-400'>{$fmtTotal}</strong></span>";
                                        }

                                        // Jika setelah difilter kosong
                                        if (empty($output)) {
                                            return "<span class='text-sm text-gray-500 dark:text-gray-400'>-</span>";
                                        }

                                        return implode('<br>', $output);
                                    }),

                                // 4. Total Amount (Sum dari total_amount_snapshot)
                                TextEntry::make('total_amount_summary')
                                    ->label('Total Nilai Penerimaan')
                                    ->weight(FontWeight::ExtraBold)
                                    ->color('success') // Secara otomatis menggunakan text-success-600 dark:text-success-400
                                    ->money('IDR', locale: 'id')
                                    ->getStateUsing(function ($record) {
                                        return $record->deliveryOrderReceiptDetails->sum('total_amount_snapshot');
                                    }),

                                TextEntry::make('total_po_summary')
                                    ->label('Total Nilai Keseluruhan PO')
                                    ->weight(FontWeight::ExtraBold)
                                    ->color('info') // Gunakan warna berbeda (misal: biru/info) agar beda dengan nilai penerimaan
                                    ->money('IDR', locale: 'id') // Format otomatis Rupiah
                                    ->getStateUsing(function ($record) {
                                        $details = $record->deliveryOrderReceiptDetails;

                                        if ($details->isEmpty()) {
                                            return 0;
                                        }

                                        // 1. Ambil ID PO yang unik dari detail penerimaan ini
                                        // Menggunakan unique() karena satu DO bisa memuat beberapa item dari PO yang sama.
                                        // Kita hanya ingin menjumlahkan total PO-nya satu kali saja.
                                        $poIds = $details->pluck('purchase_order_issued_id')->unique()->filter();

                                        // 2. Jumlahkan field total_amount_in_lc dari tabel PurchaseOrderIssued
                                        $totalPoValue = PurchaseOrderIssued::whereIn('id', $poIds)
                                            ->sum('total_amount_in_lc');

                                        return (float) $totalPoValue;
                                    }),
                            ]),
                        ]),

                    Section::make('Referensi Monitoring')
                        ->icon(Heroicon::OutlinedKey)
                        ->schema([
                            TextEntry::make('npk_monitoring_id')
                                ->label('ID Monitoring NPK')
                                ->icon('heroicon-m-link')
                                ->placeholder('Tidak tertaut')
                                ->url(fn ($record) => $record->npk_monitoring_id ? url('/admin/monitoring-npk/'.$record->npk_monitoring_id) : null, true)
                                ->color(fn ($state) => $state ? 'primary' : 'gray'),

                            TextEntry::make('chemical_monitoring_id')
                                ->label('ID Monitoring Chemical')
                                ->icon('heroicon-m-link')
                                ->placeholder('Tidak tertaut')
                                ->url(fn ($record) => $record->chemical_monitoring_id ? url('/admin/monitoring-chemical/'.$record->chemical_monitoring_id) : null, true)
                                ->color(fn ($state) => $state ? 'primary' : 'gray'),
                        ])
                        ->collapsed(),
                ]),
            ]);
    }
}
