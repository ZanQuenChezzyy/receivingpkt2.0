<?php

namespace App\Filament\Resources\GrsRdtvs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class GrsRdtvInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Informasi Eksekusi')
                    ->description('Rincian informasi mengenai eksekusi GRS & RDTV ini.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('transaction_date')
                                ->label('Tanggal Eksekusi')
                                ->icon('heroicon-m-calendar')
                                ->date('d F Y')
                                ->weight(FontWeight::Bold),

                            TextEntry::make('category')
                                ->label('Kategori Dokumen')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'GRS' => 'success',
                                    'RDTV' => 'warning',
                                    default => 'gray',
                                })
                                ->icon(fn (string $state): string => match ($state) {
                                    'GRS' => 'heroicon-m-document-check',
                                    'RDTV' => 'heroicon-m-arrow-path',
                                    default => 'heroicon-m-document',
                                }),

                            TextEntry::make('createdBy.name')
                                ->label('Dieksekusi Oleh')
                                ->icon('heroicon-m-user-circle'),
                        ]),
                    ]),

                Section::make('Informasi Sistem')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('created_at')
                                ->label('Waktu Dibuat')
                                ->dateTime('d F Y H:i')
                                ->placeholder('-'),
                            TextEntry::make('updated_at')
                                ->label('Terakhir Diperbarui')
                                ->dateTime('d F Y H:i')
                                ->placeholder('-'),
                        ]),
                    ])->collapsed(),
            ]);
    }
}
