<?php

namespace App\Filament\Resources\DeliveryOrderReceipts\RelationManagers;

use App\Models\Transmittal;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class QcHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'qcHistories';

    protected static ?string $title = 'Riwayat QC';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal/Waktu')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Kirim' => 'warning',
                        'Revisi' => 'danger',
                        'Kembali' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('notes')
                    ->label('Keterangan / Alasan')
                    ->html()
                    ->wrap(),
                TextColumn::make('createdBy.name')
                    ->label('Oleh')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('log_revisi')
                    ->label('Log Revisi ISTEK')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Alasan Revisi / Pengembalian')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        $livewire->getOwnerRecord()->qcHistories()->create([
                            'status' => 'Revisi',
                            'notes' => $data['notes'],
                            'created_by' => Auth::user()->id ?? 1,
                        ]);
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(fn ($record) => str_contains($record->notes ?? '', 'No: TRM-') ? 'Hapus Riwayat & Keluarkan dari Transmittal?' : 'Hapus Riwayat')
                    ->modalDescription(fn ($record) => str_contains($record->notes ?? '', 'No: TRM-') ? 'Riwayat ini terhubung dengan Transmittal. Menghapus log ini juga akan otomatis mengeluarkan DO dari Transmittal tersebut. Anda yakin?' : 'Anda yakin ingin menghapus riwayat ini?')
                    ->before(function ($record, RelationManager $livewire) {
                        if (preg_match('/No: (TRM-[A-Z0-9-]+)\)/', $record->notes ?? '', $matches)) {
                            $transmittal = Transmittal::where('transmittal_no', $matches[1])->first();
                            if ($transmittal) {
                                $livewire->getOwnerRecord()->transmittals()->detach($transmittal->id);
                            }
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (Collection $records, RelationManager $livewire) {
                            foreach ($records as $record) {
                                if (preg_match('/No: (TRM-[A-Z0-9-]+)\)/', $record->notes ?? '', $matches)) {
                                    $transmittal = Transmittal::where('transmittal_no', $matches[1])->first();
                                    if ($transmittal) {
                                        $livewire->getOwnerRecord()->transmittals()->detach($transmittal->id);
                                    }
                                }
                            }
                        }),
                ]),
            ]);
    }
}
