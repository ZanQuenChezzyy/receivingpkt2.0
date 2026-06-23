<?php

namespace App\Filament\Resources\Transmittals\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class TransmittalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Informasi Transmittal')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Grid::make(3)->schema([
                                TextEntry::make('transmittal_no')
                                    ->label('Nomor Transmittal')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-m-qr-code')
                                    ->copyable(),

                                TextEntry::make('type')
                                    ->label('Tipe Transmittal')
                                    ->badge()
                                    ->color(fn ($state) => $state === 'Kirim' ? 'primary' : 'warning')
                                    ->icon(fn ($state) => $state === 'Kirim' ? 'heroicon-m-paper-airplane' : 'heroicon-m-arrow-uturn-left'),

                                TextEntry::make('destination')
                                    ->label('Tujuan')
                                    ->badge()
                                    ->color(fn ($state) => $state === 'ISTEK' ? 'info' : 'success')
                                    ->icon('heroicon-m-building-office'),

                                TextEntry::make('createdBy.name')
                                    ->label('Dibuat Oleh')
                                    ->icon('heroicon-m-user-circle'),

                                TextEntry::make('created_at')
                                    ->label('Tanggal Pembuatan')
                                    ->date('l, d F Y')
                                    ->icon('heroicon-m-calendar-days'),

                                TextEntry::make('total_documents')
                                    ->getStateUsing(fn ($record) => $record->transmittalItems()->count())
                                    ->label('Total Dokumen')
                                    ->badge()
                                    ->color('gray')
                                    ->icon('heroicon-m-document-duplicate'),
                            ]),
                        ]),

                    Section::make('Daftar Dokumen DO')
                        ->icon('heroicon-o-document-duplicate')
                        ->schema([
                            RepeatableEntry::make('transmittalItems')
                                ->hiddenLabel()
                                ->schema([
                                    Grid::make(3)->schema([
                                        TextEntry::make('deliveryOrderReceipt.delivery_oder_no')
                                            ->label('No. Surat Jalan (DO)')
                                            ->weight(FontWeight::Bold)
                                            ->color('gray'),

                                        TextEntry::make('deliveryOrderReceipt.document_code')
                                            ->label('Kode Dokumen')
                                            ->weight(FontWeight::SemiBold)
                                            ->color('primary'),

                                        TextEntry::make('deliveryOrderReceipt.post_103')
                                            ->label('Status MIGO 103')
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => $state ? 'Sudah Post' : 'Belum Post')
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->icon(fn ($state) => $state ? 'heroicon-m-check-circle' : 'heroicon-m-clock'),
                                    ]),
                                ])->columns(1),
                        ]),
                ])->columnSpanFull(),
            ]);
    }
}
