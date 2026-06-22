<?php

namespace App\Filament\Resources\GrsRdtvs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GrsRdtvForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi GRS & RDTV')
                    ->description('Pilih Tanggal Transaksi dan kategori dokumen penagihan')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->placeholder('Masukkan Tanggal')
                            ->native(false)
                            ->prefixIcon('heroicon-m-calendar')
                            ->default(now())
                            ->required(),
                        ToggleButtons::make('category')
                            ->label('Kategori Dokumen')
                            ->options([
                                'GRS' => 'GRS',
                                'RDTV' => 'RDTV',
                            ])
                            ->colors([
                                'GRS' => 'success',
                                'RDTV' => 'warning',
                            ])
                            ->icons([
                                'GRS' => 'heroicon-m-document-check',
                                'RDTV' => 'heroicon-m-arrow-path',
                            ])
                            ->inline()
                            ->required(),
                        Hidden::make('created_by')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),

                Section::make('Unggah Dokumen')
                    ->description('Unggah puluhan dokumen PDF sekaligus. Sistem akan otomatis menautkan ke DO berdasarkan Nama File.')
                    ->icon('heroicon-o-document-arrow-up')
                    ->schema([
                        FileUpload::make('files')
                            ->label('Pilih File PDF')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf'])
                            ->storeFiles(false)
                            ->required()
                            ->helperText(str('**Format Nama File**: `5300057474-10-5208-17062026.pdf` (Harus persis sama dengan Kode Dokumen di Sistem)')
                                ->inlineMarkdown()
                                ->toHtmlString()),
                    ]),
            ]);
    }
}
