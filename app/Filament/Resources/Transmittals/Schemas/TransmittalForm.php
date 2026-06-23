<?php

namespace App\Filament\Resources\Transmittals\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransmittalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transmittal')
                    ->schema([
                        TextInput::make('transmittal_no')
                            ->label('Nomor Transmittal')
                            ->disabled()
                            ->dehydrated(false) // Don't save it again if disabled, though not strictly necessary
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Tipe Transmittal')
                            ->options([
                                'Kirim' => 'Kirim',
                                'Kembali' => 'Kembali',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('destination')
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
