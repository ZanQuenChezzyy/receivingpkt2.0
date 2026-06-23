<?php

namespace App\Filament\Resources\DeliveryOrderReceipts\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class DeliveryOrderReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 📄 GRUP 1: INFORMASI DOKUMEN
                ColumnGroup::make('Informasi Dokumen', [
                    TextColumn::make('po_and_do')
                        ->label('Nomor PO & DO')
                        ->icon('heroicon-m-document-duplicate')
                        ->iconColor('primary')
                        ->color('primary')
                        ->weight(FontWeight::Bold)
                        ->getStateUsing(fn($record) => $record->deliveryOrderReceiptDetails->first()?->purchaseOrderIssued?->purchase_order_no ?? 'Tanpa PO')
                        ->description(function ($record) {
                            $doInfo = "<span class='text-gray-500 font-medium'>DO: {$record->delivery_oder_no}</span>";
                            $seqInfo = $record->arrival_sequence ? "<br><span class='text-blue-600 text-xs font-bold'>Kedatangan Ke-{$record->arrival_sequence}</span>" : '';

                            return new HtmlString($doInfo . $seqInfo);
                        })
                        ->searchable(query: function (Builder $query, string $search) {
                            $query->where('delivery_oder_no', 'like', "%{$search}%")
                                ->orWhereHas('deliveryOrderReceiptDetails.purchaseOrderIssued', function ($q) use ($search) {
                                    $q->where('purchase_order_no', 'like', "%{$search}%");
                                });
                        })
                        ->copyable()
                        ->copyMessage('Nomor PO disalin!')
                        ->sortable(),

                    TextColumn::make('document_code')
                        ->label('Kode Dokumen')
                        ->icon(Heroicon::QrCode)
                        ->iconColor('primary')
                        ->color('primary')
                        ->searchable()
                        ->copyable()
                        ->weight(FontWeight::SemiBold)
                        ->copyMessage('Kode dokumen disalin!')
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('received_date')
                        ->label('Tanggal Terima')
                        ->icon(Heroicon::CalendarDays)
                        ->iconColor('gray')
                        ->date('d F Y')
                        ->description(fn($record) => Carbon::parse($record->received_date)->translatedFormat('l')) // Nama hari di bawahnya
                        ->sortable(),
                ]),

                // 📦 GRUP 2: DETAIL PENERIMAAN
                ColumnGroup::make('Detail Penerimaan', [
                    TextColumn::make('deskripsi_item')
                        ->label('Material')
                        ->icon(Heroicon::Cube)
                        ->iconColor('gray')
                        ->getStateUsing(function ($record) {
                            $details = $record->deliveryOrderReceiptDetails;
                            if ($details->isEmpty()) {
                                return ['Tidak ada item'];
                            }

                            return $details->map(function ($detail) {
                                return $detail->description;
                            })->toArray();
                        })
                        ->listWithLineBreaks() // Menampilkan data array berbaris ke bawah
                        ->bulleted() // Menambahkan titik (bullet)
                        ->limitList(2) // Batasi tampilan awal misal 2 baris agar tabel tidak terlalu panjang
                        ->limit(15)
                        ->expandableLimitedList() // Bisa diklik "View more"
                        ->searchable(query: function (Builder $query, string $search) {
                            $query->whereHas('deliveryOrderReceiptDetails', function ($q) use ($search) {
                                $q->where('description', 'like', "%{$search}%");
                            });
                        })
                        ->tooltip(function ($record) {
                            $details = $record->deliveryOrderReceiptDetails;

                            $htmlList = '';
                            foreach ($details->pluck('description') as $index => $desc) {
                                $number = $index + 1;
                                $htmlList .= "{$number}. {$desc}<br>"; // Gunakan <br> sebagai enter HTML
                            }

                            return new HtmlString($htmlList);
                        }),

                    TextColumn::make('quantity_item')
                        ->label('Quantity')
                        ->iconColor('success')
                        ->getStateUsing(function ($record) {
                            $details = $record->deliveryOrderReceiptDetails;
                            if ($details->isEmpty()) {
                                return ['-'];
                            }

                            return $details->map(function ($detail) {
                                $qty = number_format($detail->quantity, 0, ',', '.');

                                return "{$qty} {$detail->uoi}";
                            })->toArray();
                        })
                        ->listWithLineBreaks() // Harus sama persis dengan kolom sebelahnya
                        ->limitList(2)
                        ->expandableLimitedList()
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->color('success') // Memberikan warna hijau pada teks quantity agar kontras
                        ->weight(FontWeight::SemiBold),

                    TextColumn::make('lokasi')
                        ->label('Lokasi')
                        ->icon('heroicon-m-map-pin')
                        ->getStateUsing(fn($record) => $record->deliveryOrderReceiptDetails->first()?->locationReceiving?->name ?? 'Belum Diatur')
                        ->badge()
                        ->color('info'),

                    TextColumn::make('status_pengambilan')
                        ->label('Status Pengambilan')
                        ->icon('heroicon-m-arrow-right-on-rectangle')
                        ->getStateUsing(function ($record) {
                            $details = $record->deliveryOrderReceiptDetails;
                            if ($details->isEmpty()) {
                                return 'Belum Diambil';
                            }

                            $totalReceived = $details->sum('quantity');
                            $totalIssued = $details->sum(function ($d) {
                                return $d->materialIssueDetails->sum('diserahkan');
                            });

                            if ($totalIssued == 0) {
                                return 'Belum Diambil';
                            }
                            if ($totalIssued >= $totalReceived) {
                                return 'Full Diambil';
                            }

                            return 'Sebagian Diambil';
                        })
                        ->description(function ($record) {
                            $details = $record->deliveryOrderReceiptDetails;
                            if ($details->isEmpty()) {
                                return '';
                            }

                            $totalReceived = $details->sum('quantity');
                            $totalIssued = $details->sum(function ($d) {
                                return $d->materialIssueDetails->sum('diserahkan');
                            });

                            if ($totalReceived == 0) {
                                return '';
                            }
                            $percentage = round(($totalIssued / $totalReceived) * 100);

                            return "{$percentage}% ({$totalIssued} dari {$totalReceived})";
                        })
                        ->badge()
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->color(fn($state) => match ($state) {
                            'Belum Diambil' => 'danger',
                            'Sebagian Diambil' => 'warning',
                            'Full Diambil' => 'success',
                            default => 'gray',
                        }),

                    // Dipindahkan ke sini dari Log Sistem
                    TextColumn::make('receivedBy.name')
                        ->label('Penerima')
                        ->icon(Heroicon::User)
                        ->iconColor('warning')
                        ->color('warning')
                        ->badge()
                        ->searchable()
                        ->sortable(),
                ]),

                // ⚙️ GRUP 3: STATUS & TAHAPAN
                ColumnGroup::make('Status & Tahapan', [
                    TextColumn::make('stage')
                        ->label('Termin / Tahapan')
                        ->placeholder('Tidak Ada Tahapan')
                        ->badge()
                        ->default('')
                        // 🌟 TAMBAHKAN $record DI SINI
                        ->formatStateUsing(function ($state, $record) {
                            if (empty($state)) {
                                return 'Default';
                            }
                            if (str_contains(strtoupper($state), 'DOF')) {
                                return 'Surat DOF';
                            }
                            if (str_contains(strtoupper($state), 'TERMIN')) {
                                // Ambil nilai persentase, gunakan (float) agar angka 15.00 menjadi 15 (lebih rapi)
                                $percentage = (float) $record->termin_percentage;

                                return "{$state}: {$percentage}%";
                            }

                            return $state;
                        })
                        ->color(function ($state) {
                            if (empty($state)) {
                                return 'success';
                            }
                            if (str_contains(strtoupper($state), 'DOF')) {
                                return 'info';
                            }
                            if (str_contains(strtoupper($state), 'TERMIN')) {
                                return 'warning';
                            }

                            return 'gray';
                        })
                        ->icon(function ($state) {
                            if (empty($state)) {
                                return 'heroicon-m-check-circle';
                            }
                            if (str_contains(strtoupper($state), 'DOF')) {
                                return 'heroicon-m-document-duplicate';
                            }
                            if (str_contains(strtoupper($state), 'TERMIN')) {
                                return 'heroicon-m-chart-pie';
                            }

                            return 'heroicon-m-tag';
                        })
                        ->searchable(),

                    TextColumn::make('post_103')
                        ->label('Status POST 103')
                        ->badge()
                        ->default(false) // Wajib ditambah agar null tetap dirender sebagai 'Belum Post'
                        ->formatStateUsing(fn($state) => $state ? 'Posted 103' : 'Belum Post')
                        ->description(fn($record) => $record->post_103 ? Carbon::parse($record->post_103)->format('d M Y - H:i') : 'Menunggu aksi')
                        ->color(fn($state) => $state ? 'success' : 'gray')
                        ->icon(fn($state) => $state ? 'heroicon-m-check-badge' : 'heroicon-m-clock')
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->sortable(),

                    TextColumn::make('is_physically_received')
                        ->label('Kedatangan Fisik')
                        ->getStateUsing(fn($record) => $record->receipt_mode === 'Standard' ? true : $record->is_physically_received)
                        ->formatStateUsing(fn($state) => $state ? 'Fisik Tiba' : 'Menunggu / Transit')
                        ->badge()
                        ->color(fn($state) => $state ? 'success' : 'warning')
                        ->icon(fn($state) => $state ? 'heroicon-m-check-badge' : 'heroicon-m-truck')
                        ->description(function ($record) {
                            if ($record->receipt_mode === 'Standard' || $record->is_physically_received) {
                                return null;
                            }
                            $loc = $record->current_location ?? 'Transit';
                            $incoterm = $record->incoterms ? " ({$record->incoterms})" : '';

                            return new HtmlString("<span class='text-xs'>Posisi: {$loc}{$incoterm}</span>");
                        })
                        ->toggleable(isToggledHiddenByDefault: true)
                        ->sortable(),
                ]),

                // 👤 GRUP 4: LOG SISTEM (Bisa disembunyikan user)
                ColumnGroup::make('Log Sistem', [
                    TextColumn::make('createdBy.name')
                        ->label('Dibuat Oleh')
                        ->icon('heroicon-m-computer-desktop')
                        ->color('gray')
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('created_at')
                        ->label('Tgl Dibuat')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('updated_at')
                        ->label('Tgl Diperbarui')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('post_103_action')
                        ->label('Post 103')
                        ->icon(Heroicon::DocumentCheck)
                        ->color('success')
                        ->outlined()
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Post 103')
                        ->modalDescription('Apakah Anda yakin ingin melakukan Post 103 pada dokumen ini? Tanggal hari ini akan tercatat sebagai tanggal post.')
                        ->modalSubmitActionLabel('Ya, Post Sekarang')
                        ->hidden(fn($record): bool => $record->post_103 !== null)
                        ->action(function ($record) {
                            $record->update([
                                'post_103' => Carbon::now(),
                            ]);

                            Notification::make()
                                ->title('Berhasil!')
                                ->body('Tanggal Post 103 berhasil dicatat.')
                                ->success()
                                ->send();
                        }),
                    Action::make('undo_post_103_action')
                        ->label('Batal Post')
                        ->icon(Heroicon::ArrowUturnLeft)
                        ->color('danger')
                        ->outlined()
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Post 103')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan Post 103? Data tanggal post sebelumnya akan dihapus dari dokumen ini.')
                        ->modalSubmitActionLabel('Ya, Batalkan')
                        ->hidden(fn($record): bool => $record->post_103 === null)
                        ->action(function ($record) {
                            $record->update([
                                'post_103' => null,
                            ]);

                            Notification::make()
                                ->title('Dibatalkan!')
                                ->body('Status Post 103 berhasil ditarik kembali.')
                                ->warning()
                                ->send();
                        }),
                    Action::make('pending_dokumen_action')
                        ->label('Pending Dokumen')
                        ->icon(Heroicon::Clock)
                        ->color('warning')
                        ->outlined()
                        ->schema([
                            Select::make('delay_reason')
                                ->label('Alasan Penundaan POST 103')
                                ->placeholder('Pilih Alasan Penundaan Dokumen')
                                ->native(false)
                                ->options([
                                    'PO Belum Confirm' => 'PO Belum Confirm',
                                    'Barang Diambil User Langsung (Tanpa Monitor)' => 'Barang Diambil User Langsung (Tanpa Monitor)',
                                    'Fisik Kelebihan Kirim (Over-delivery)' => 'Fisik Kelebihan Kirim (Over-delivery)',
                                    'Lainnya' => 'Lainnya',
                                ])
                                ->required()
                                ->live(),
                            Textarea::make('delay_notes')
                                ->label('Catatan Penundaan (Lainnya)')
                                ->placeholder('Masukkan Alasan Penundaan Lainnya')
                                ->autosize()
                                ->rows(3)
                                ->visible(fn(Get $get) => $get('delay_reason') === 'Lainnya')
                                ->required(fn(Get $get) => $get('delay_reason') === 'Lainnya'),
                        ])
                        ->modalHeading('Pending Dokumen')
                        ->modalDescription('Masukkan alasan penundaan proses dokumen ini.')
                        ->modalSubmitActionLabel('Simpan')
                        ->hidden(fn($record): bool => $record->status === 'Pending')
                        ->action(function (array $data, $record) {
                            $record->update([
                                'status' => 'Pending',
                                'delay_reason' => $data['delay_reason'],
                                'delay_notes' => $data['delay_notes'] ?? null,
                                'pending_date' => Carbon::now(),
                                'pending_resolved_date' => null,
                            ]);

                            Notification::make()
                                ->title('Dokumen Di-pending!')
                                ->body('Dokumen berhasil ditandai sebagai Pending.')
                                ->warning()
                                ->send();
                        }),
                    Action::make('undo_pending_action')
                        ->label('Batal Pending')
                        ->icon(Heroicon::ArrowUturnLeft)
                        ->color('success')
                        ->outlined()
                        ->requiresConfirmation()
                        ->modalHeading('Batalkan Status Pending')
                        ->modalDescription('Apakah Anda yakin ingin membatalkan status Pending pada dokumen ini? Dokumen akan kembali diproses seperti biasa.')
                        ->modalSubmitActionLabel('Ya, Batalkan')
                        ->visible(fn($record): bool => $record->status === 'Pending')
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'Diterima',
                                'pending_resolved_date' => Carbon::now(),
                            ]);

                            Notification::make()
                                ->title('Pending Dibatalkan!')
                                ->body('Status dokumen telah kembali normal.')
                                ->success()
                                ->send();
                        }),
                ])
                    ->label('Tindakan')
                    ->icon(Heroicon::QueueList)
                    ->color('primary')
                    ->button()
                    ->outlined(),

                ActionGroup::make([
                    ViewAction::make()
                        ->color('gray')
                        ->slideOver(),
                    EditAction::make()
                        ->color('info')
                        ->slideOver(),
                    DeleteAction::make()
                        ->requiresConfirmation(),
                ])
                    ->label('')
                    ->icon(Heroicon::EllipsisHorizontal)
                    ->size(Size::Small)
                    ->color('info')
                    ->outlined()
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada Dokumen Penerimaan')
            ->emptyStateDescription('Buat dokumen penerimaan barang baru dari PO yang tersedia.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
