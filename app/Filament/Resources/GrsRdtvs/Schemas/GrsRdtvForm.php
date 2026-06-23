<?php

namespace App\Filament\Resources\GrsRdtvs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

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
                            ->live()
                            ->required(),
                        Hidden::make('created_by')
                            ->default(fn() => Auth::id()),
                    ])->columns(2),

                Section::make('Unggah Dokumen')
                    ->description('Anda dapat mengunggah puluhan dokumen sekaligus jika GRS, dan untuk RDTV Anda harus memasukkan alasan penolakan pada tiap dokumen.')
                    ->icon('heroicon-o-document-arrow-up')
                    ->schema([
                        FileUpload::make('files')
                            ->label('Pilih File PDF Sekaligus (GRS)')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf'])
                            ->storeFiles(false)
                            ->required(fn(Get $get) => $get('category') === 'GRS')
                            ->helperText(str('**Format Nama File**: `5300057474-10-5208-17062026.pdf` (Harus persis sama dengan Kode Dokumen di Sistem)')
                                ->inlineMarkdown()
                                ->toHtmlString())
                            ->visible(fn(Get $get) => $get('category') === 'GRS'),

                        Repeater::make('items')
                            ->label('Unggah Dokumen RDTV & Alasan')
                            ->schema([
                                FileUpload::make('file')
                                    ->label('Pilih File PDF')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->storeFiles(false)
                                    ->required()
                                    ->helperText(str('**Format Nama File**: `5300057474-10-5208-17062026.pdf` (Harus persis sama dengan Kode Dokumen di Sistem)')
                                        ->inlineMarkdown()
                                        ->toHtmlString()),
                                Textarea::make('reason')
                                    ->label('Alasan Penolakan')
                                    ->placeholder('Masukkan Alasan Penolakan Dokumen RDTV')
                                    ->autosize()
                                    ->required()
                                    ->rows(1)
                            ])
                            ->visible(fn(Get $get) => $get('category') === 'RDTV')
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Dokumen Lainnya')
                    ]),
            ]);
    }
}
