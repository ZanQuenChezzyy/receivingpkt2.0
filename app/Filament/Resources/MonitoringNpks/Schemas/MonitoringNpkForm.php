<?php

namespace App\Filament\Resources\MonitoringNpks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class MonitoringNpkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)->schema([
                    Group::make()->schema([
                        Section::make('Informasi Utama')
                            ->description('Detail Purchase Order dan Kedatangan NPK.')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('purchase_order_terbit_id')
                                    ->label('ID PO Terbit')
                                    ->required()
                                    ->numeric(),
                                TextInput::make('delivery_oder_number')
                                    ->label('Nomor DO'),
                                Select::make('location_id')
                                    ->label('Lokasi')
                                    ->relationship('location', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                DatePicker::make('delivery_oder_delivery_date')
                                    ->label('Tanggal Kirim DO')
                                    ->native(false),
                                DatePicker::make('received_date')
                                    ->label('Tanggal Diterima')
                                    ->native(false),
                                FileUpload::make('document_path')
                                    ->label('Upload DO')
                                    ->directory('monitoring-npk-docs')
                                    ->maxSize(5120)
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Section::make('Progress & Status PO')
                            ->description('Pantau status dan tahapan proses.')
                            ->icon('heroicon-o-arrow-path')
                            ->schema([
                                TextInput::make('stage')
                                    ->label('Tahapan (Stage)'),
                                TextInput::make('purchase_order_status')
                                    ->label('Status PO'),
                                DatePicker::make('purchase_order_103_date')
                                    ->label('Tanggal PO 103')
                                    ->native(false),
                                DatePicker::make('purchase_order_status_a_date')
                                    ->label('Tanggal Status A')
                                    ->native(false),
                                DatePicker::make('purchase_order_status_b_date')
                                    ->label('Tanggal Status B')
                                    ->native(false),
                                TextInput::make('purchase_order_status_a_files')
                                    ->label('File Status A')
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                    Group::make()->schema([
                        Section::make('Quality Control (QC)')
                            ->description('Informasi terkait sample, Laprima, dan COA.')
                            ->icon('heroicon-o-beaker')
                            ->schema([
                                DatePicker::make('sample_receivied_date')
                                    ->label('Tanggal Terima Sample')
                                    ->native(false),
                                DatePicker::make('laprima_date')
                                    ->label('Tanggal Laprima')
                                    ->native(false),
                                DatePicker::make('coa_date')
                                    ->label('Tanggal COA')
                                    ->native(false),
                                TextInput::make('coa_files')
                                    ->label('File COA'),
                            ]),

                        Section::make('Status Akhir')
                            ->schema([
                                Select::make('doc_status')
                                    ->label('Status Dokumen')
                                    ->options([
                                        'Outstanding' => 'Outstanding',
                                        'Completed' => 'Completed',
                                    ])
                                    ->required()
                                    ->default('Outstanding')
                                    ->native(false),
                            ]),

                        Hidden::make('created_by')
                            ->default(fn () => Auth::id() ?? 1)
                            ->required(),
                    ])->columnSpan(['lg' => 1]),
                ])->columnSpanFull(),
            ]);
    }
}
