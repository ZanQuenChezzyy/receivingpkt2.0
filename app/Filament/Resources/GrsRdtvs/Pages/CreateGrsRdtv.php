<?php

namespace App\Filament\Resources\GrsRdtvs\Pages;

use App\Filament\Resources\GrsRdtvs\GrsRdtvResource;
use App\Models\DeliveryOrderReceipt;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateGrsRdtv extends CreateRecord
{
    protected static string $resource = GrsRdtvResource::class;

    protected array $uploadedFiles = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pindahkan files ke property sementara agar tidak di-insert ke tabel grs_rdtvs
        if (isset($data['files'])) {
            $this->uploadedFiles = $data['files'];
            unset($data['files']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $grsRdtv = $this->record;
        $category = $grsRdtv->category;

        $matchedCount = 0;
        $notFoundCount = 0;

        foreach ($this->uploadedFiles as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                // Ekstrak nama asli file (misal: 5300057474-10-5208-17062026.pdf)
                $originalName = $file->getClientOriginalName();
                $documentCode = pathinfo($originalName, PATHINFO_FILENAME); // Tanpa ekstensi .pdf

                // Pindahkan file ke storage (permanen)
                $path = $file->storeAs('grs-rdtv-docs', $originalName, 'public');

                // Cari DO yang sesuai
                $do = DeliveryOrderReceipt::where('document_code', $documentCode)->first();

                if ($do) {
                    // Tautkan & Ubah status
                    $do->update(['status' => $category]);
                    $matchedCount++;
                } else {
                    $notFoundCount++;
                }

                // Catat ke item
                $grsRdtv->grsRdtvItems()->create([
                    'delivery_order_receipt_id' => $do ? $do->id : null,
                    'document_code' => $documentCode,
                    'file_path' => $path,
                    'status' => $do ? 'Matched' : 'Not Found',
                ]);
            }
        }

        Notification::make()
            ->title('Proses Selesai')
            ->body("Berhasil memproses dokumen. Matched: {$matchedCount}, Not Found: {$notFoundCount}")
            ->success()
            ->send();
    }
}
