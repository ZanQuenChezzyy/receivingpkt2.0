<?php

namespace App\Filament\Resources\Transmittals\Schemas;

use Filament\Schemas\Schema;

class TransmittalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Transmittal')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('transmittal_no')
                            ->label('Nomor Transmittal')
                            ->disabled()
                            ->dehydrated(false) // Don't save it again if disabled, though not strictly necessary
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Select::make('type')
                            ->label('Tipe Transmittal')
                            ->options([
                                'Kirim' => 'Kirim',
                                'Kembali' => 'Kembali',
                            ])
                            ->required()
                            ->native(false),

                        \Filament\Forms\Components\Select::make('destination')
                            ->label('Tujuan')
                            ->options([
                                'ISTEK' => 'ISTEK',
                                'PPE' => 'PPE',
                            ])
                            ->required()
                            ->native(false),
                    ])->columns(2),
            ]);
    }
}
