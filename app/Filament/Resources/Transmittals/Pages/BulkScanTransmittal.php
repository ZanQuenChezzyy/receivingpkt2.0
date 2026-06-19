<?php

namespace App\Filament\Resources\Transmittals\Pages;

use App\Filament\Resources\Transmittals\TransmittalResource;
use App\Models\DeliveryOrderReceipt;
use App\Models\Transmittal;
use App\Models\TransmittalItem;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BulkScanTransmittal extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TransmittalResource::class;

    protected string $view = 'filament.resources.transmittals.pages.bulk-scan-transmittal';

    public ?array $data = [];

    // Scanner states
    public $step = 1;

    public $scanned_document = '';

    public $scanned_103 = '';

    public $pending_do_id = null;

    public $pending_do_no = '';

    public ?int $transmittalId = null;

    public function mount(): void
    {
        $id = request()->query('id');
        if ($id) {
            $transmittal = Transmittal::find($id);
            if ($transmittal) {
                $this->transmittalId = $transmittal->id;

                $this->form->fill([
                    'tanggal' => $transmittal->created_at->format('Y-m-d'),
                    'type' => $transmittal->type,
                    'destination' => $transmittal->destination,
                ]);

                return;
            }
        }

        $this->form->fill([
            'tanggal' => now()->toDateString(),
            'type' => 'Kirim',
            'destination' => 'ISTEK',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Grid::make(3)->schema([
                    DatePicker::make('tanggal')
                        ->label('Pilih Tanggal')
                        ->default(now())
                        ->native(false)
                        ->required()
                        ->disabled(fn () => $this->transmittalId !== null)
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->resetScanState();
                            if ($this->transmittalId) {
                                Transmittal::find($this->transmittalId)?->update(['created_at' => \Carbon\Carbon::parse($state)->startOfDay()]);
                            }
                        }),

                    ToggleButtons::make('type')
                        ->label('Tipe Transmittal')
                        ->options([
                            'Kirim' => 'Kirim',
                            'Kembali' => 'Kembali',
                        ])
                        ->colors([
                            'Kirim' => 'primary',
                            'Kembali' => 'warning',
                        ])
                        ->inline()
                        ->required()
                        ->default('Kirim')
                        ->disabled(fn () => $this->transmittalId !== null)
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->resetScanState();
                            if ($this->transmittalId) {
                                Transmittal::find($this->transmittalId)?->update(['type' => $state]);
                            }
                        }),

                    ToggleButtons::make('destination')
                        ->label('Tujuan')
                        ->options([
                            'ISTEK' => 'ISTEK',
                            'PPE' => 'PPE',
                        ])
                        ->colors([
                            'ISTEK' => 'info',
                            'PPE' => 'success',
                        ])
                        ->inline()
                        ->required()
                        ->default('ISTEK')
                        ->disabled(fn () => $this->transmittalId !== null)
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->resetScanState();
                            if ($this->transmittalId) {
                                Transmittal::find($this->transmittalId)?->update(['destination' => $state]);
                            }
                        }),
                ]),
            ]);
    }

    public function resetScanState()
    {
        $this->step = 1;
        $this->scanned_document = '';
        $this->scanned_103 = '';
        $this->pending_do_id = null;
        $this->pending_do_no = '';
    }

    public function submitDocumentScan()
    {
        if (empty($this->data['type']) || empty($this->data['destination']) || empty($this->data['tanggal'])) {
            $this->dispatch('play-error-sound');
            Notification::make()
                ->title('Pengaturan Belum Lengkap')
                ->body('Pastikan Anda telah memilih Tanggal, Tipe, dan Tujuan Transmittal.')
                ->danger()
                ->send();

            return;
        }

        $code = trim($this->scanned_document);
        $this->scanned_document = ''; // Reset input

        if (empty($code)) {
            return;
        }

        $doReceipt = DeliveryOrderReceipt::where('delivery_oder_no', $code)
            ->orWhere('document_code', $code) // Bisa scan nomor DO atau document code (QR Dokumen)
            ->first();

        if (! $doReceipt) {
            $this->dispatch('play-error-sound');
            Notification::make()
                ->title('Tidak ditemukan')
                ->body("Dokumen dengan nomor {$code} tidak ditemukan.")
                ->danger()
                ->send();

            return;
        }

        // Jika tipe Kembali, langsung proses tanpa perlu scan 103
        if ($this->data['type'] === 'Kembali') {
            $this->processTransmittal($doReceipt);
        } else {
            // Tipe Kirim: Perlu dual-scan
            $this->pending_do_id = $doReceipt->id;

            // Ambil nomor PO dari relasi
            $detail = $doReceipt->deliveryOrderReceiptDetails()->first();
            $po_no = $detail ? ($detail->purchaseOrderIssued->purchase_order_no ?? '') : '';
            $this->pending_do_no = $po_no ?: ($doReceipt->delivery_oder_no ?? $code);

            $this->step = 2; // Lanjut ke step 2 (Scan 103)
            $this->dispatch('play-success-sound'); // Sound success untuk step 1
            $this->dispatch('focus-103-input'); // Beri tahu alpineJS untuk fokus ke input 2
        }
    }

    public function submit103Scan()
    {
        if ($this->step !== 2 || ! $this->pending_do_id) {
            return;
        }

        $code103 = trim($this->scanned_103);
        $this->scanned_103 = '';

        if (empty($code103)) {
            return;
        }

        $doReceipt = DeliveryOrderReceipt::find($this->pending_do_id);
        if (! $doReceipt) {
            $this->resetScanState();

            return;
        }

        // Update QR 103
        $doReceipt->update([
            'qr_103_code' => $code103,
        ]);

        // Proses Transmittal
        $this->processTransmittal($doReceipt);

        // Reset state ke step 1
        $this->resetScanState();
        $this->dispatch('focus-document-input');
    }

    protected function resolveTransmittal()
    {
        if ($this->transmittalId) {
            return Transmittal::find($this->transmittalId);
        }

        $tanggal = Carbon::parse($this->data['tanggal']);

        // Cari transmittal yang ada di tanggal tersebut
        $transmittal = Transmittal::where('type', $this->data['type'])
            ->where('destination', $this->data['destination'])
            ->where('created_by', Auth::user()->id ?? 1)
            ->whereDate('created_at', $tanggal->toDateString())
            ->first();

        if (! $transmittal) {
            $transmittal = Transmittal::create([
                'type' => $this->data['type'],
                'destination' => $this->data['destination'],
                'created_by' => Auth::user()->id ?? 1,
                'created_at' => $tanggal->setTimeFrom(now()),
                'transmittal_no' => 'TRM-'.$tanggal->format('Ymd').'-'.strtoupper(substr(uniqid(), -4)),
            ]);
        }

        $this->transmittalId = $transmittal->id;

        return $transmittal;
    }

    protected function processTransmittal(DeliveryOrderReceipt $doReceipt)
    {
        if (empty($this->data['type']) || empty($this->data['destination']) || empty($this->data['tanggal'])) {
            $this->dispatch('play-error-sound');
            Notification::make()
                ->title('Terjadi Kesalahan Data')
                ->body('Tipe, Tujuan, atau Tanggal kosong. Harap segarkan halaman (F5) dan coba lagi.')
                ->danger()
                ->send();

            return;
        }

        $transmittal = $this->resolveTransmittal();

        // Cek apakah item sudah ada di transmittal ini
        $exists = $transmittal->transmittalItems()->where('delivery_order_receipt_id', $doReceipt->id)->exists();

        if ($exists) {
            $this->dispatch('play-error-sound');
            Notification::make()
                ->title('Sudah di-scan')
                ->body("Dokumen {$doReceipt->delivery_oder_no} sudah ada di Transmittal {$this->data['type']} ke {$this->data['destination']} hari ini.")
                ->warning()
                ->send();

            return;
        }

        // Jika tipe transmittal bukan 'Kembali', cek apakah dokumen ini sudah pernah ada di Transmittal lain sebelumnya
        if ($this->data['type'] !== 'Kembali') {
            $previousTransmittalItem = TransmittalItem::where('delivery_order_receipt_id', $doReceipt->id)
                ->where('transmittal_id', '!=', $transmittal->id)
                ->latest()
                ->first();

            if ($previousTransmittalItem) {
                // Cek jika status terakhir di QcHistory adalah Revisi
                $latestQc = $doReceipt->qcHistories()->latest()->first();
                if ($latestQc && $latestQc->status === 'Revisi') {
                    // Otomatis gunakan alasan revisi tanpa perlu modal
                    $this->resumeProcessTransmittal($doReceipt, $transmittal, $latestQc->notes);

                    return;
                }

                // Hentikan proses dan munculkan modal untuk meminta alasan
                $this->mountAction('requireReason', [
                    'do_id' => $doReceipt->id,
                    'transmittal_id' => $transmittal->id,
                ]);

                return;
            }
        }

        // Lanjutkan jika tidak ada duplikasi di hari sebelumnya
        $this->resumeProcessTransmittal($doReceipt, $transmittal);
    }

    public function requireReasonAction(): Action
    {
        return Action::make('requireReason')
            ->modalHeading('Dokumen Sudah Pernah Diajukan')
            ->modalDescription('Sistem mendeteksi bahwa dokumen ini sudah pernah dikirim atau dikembalikan pada Transmittal sebelumnya. Harap masukkan alasan mengapa dokumen ini diajukan ulang.')
            ->form([
                Textarea::make('reason')
                    ->label('Alasan Pengajuan Ulang')
                    ->required()
                    ->maxLength(255),
            ])
            ->action(function (array $data, array $arguments) {
                $doReceipt = DeliveryOrderReceipt::find($arguments['do_id']);
                $transmittal = Transmittal::find($arguments['transmittal_id']);

                if ($doReceipt && $transmittal) {
                    $this->resumeProcessTransmittal($doReceipt, $transmittal, $data['reason']);
                    $this->resetScanState();
                    $this->dispatch('focus-document-input');
                }
            })
            ->modalCancelAction(fn ($action) => $action->label('Batal'))
            ->modalSubmitAction(fn ($action) => $action->label('Lanjutkan Transmittal'));
    }

    protected function resumeProcessTransmittal(DeliveryOrderReceipt $doReceipt, Transmittal $transmittal, ?string $reason = null)
    {
        DB::beginTransaction();
        try {
            // Tambahkan ke TransmittalItem
            $transmittal->transmittalItems()->create([
                'delivery_order_receipt_id' => $doReceipt->id,
                'status' => $this->data['type'],
            ]);

            // Siapkan catatan (notes)
            $destination = $this->data['type'] === 'Kembali' ? 'Receiving' : $this->data['destination'];
            $notes = "Di-scan melalui Transmittal {$this->data['type']} (Tujuan: {$destination})";
            if ($reason) {
                $notes .= '<br>Alasan Pengajuan Ulang: '.$reason;
            }

            // Catat ke QcHistory
            $doReceipt->qcHistories()->create([
                'status' => $this->data['type'],
                'notes' => $notes,
                'created_by' => Auth::user()->id ?? 1,
            ]);

            DB::commit();

            $this->dispatch('play-success-sound');
            Notification::make()
                ->title('Berhasil')
                ->body("Dokumen {$doReceipt->delivery_oder_no} berhasil ditambahkan.")
                ->success()
                ->send();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('play-error-sound');
            Notification::make()
                ->title('Gagal')
                ->body('Terjadi kesalahan saat memproses data.')
                ->danger()
                ->send();
        }
    }

    public function deleteItem($itemId)
    {
        $item = TransmittalItem::find($itemId);
        if ($item) {
            $doReceipt = $item->deliveryOrderReceipt;
            $transmittal = $item->transmittal;

            // Hapus log QC history yang terkait dengan transmittal ini
            if ($doReceipt && $transmittal) {
                $doReceipt->qcHistories()
                    ->where('notes', 'LIKE', "%No: {$transmittal->transmittal_no}%")
                    ->delete();
            }

            $item->delete();

            Notification::make()
                ->title('Berhasil Dihapus')
                ->body('Dokumen berhasil dikeluarkan dari transmittal.')
                ->success()
                ->send();
        }
    }

    public function getScannedItemsProperty()
    {
        if ($this->transmittalId) {
            $transmittal = Transmittal::find($this->transmittalId);
            if ($transmittal) {
                return $transmittal->transmittalItems()->with('deliveryOrderReceipt.deliveryOrderReceiptDetails.purchaseOrderIssued')->latest()->get();
            }
        }

        if (empty($this->data['type']) || empty($this->data['destination']) || empty($this->data['tanggal'])) {
            return collect(); // Return empty collection instead of array to prevent issues in blade if using collection methods
        }

        $transmittal = Transmittal::where('type', $this->data['type'])
            ->where('destination', $this->data['destination'])
            ->where('created_by', Auth::user()->id ?? 1)
            ->whereDate('created_at', \Carbon\Carbon::parse($this->data['tanggal'])->toDateString())
            ->first();

        if (! $transmittal) {
            return [];
        }

        return $transmittal->transmittalItems()->with('deliveryOrderReceipt.deliveryOrderReceiptDetails.purchaseOrderIssued')->latest()->get();
    }
}
