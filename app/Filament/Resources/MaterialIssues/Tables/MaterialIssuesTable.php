<?php

namespace App\Filament\Resources\MaterialIssues\Tables;

use App\Models\MaterialIssue;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MaterialIssuesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColumnGroup::make('Informasi Dokumen', [
                    TextColumn::make('mir_number')
                        ->label('No. MIR')
                        ->icon('heroicon-m-document-duplicate')
                        ->iconColor('primary')
                        ->color('primary')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->copyable()
                        ->sortable(),

                    TextColumn::make('tanggal')
                        ->label('Tanggal')
                        ->icon(Heroicon::CalendarDays)
                        ->iconColor('gray')
                        ->date('d F Y')
                        ->sortable(),
                ]),

                ColumnGroup::make('Detail Permintaan', [
                    TextColumn::make('purchaseOrderIssued.purchase_order_no')
                        ->label('Nomor PO')
                        ->icon('heroicon-m-shopping-cart')
                        ->weight(FontWeight::SemiBold)
                        ->searchable()
                        ->sortable(),

                    TextColumn::make('diminta_oleh')
                        ->label('Diminta Oleh')
                        ->icon('heroicon-m-user')
                        ->searchable()
                        ->sortable(),

                    TextColumn::make('npk')
                        ->label('NPK')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('materialIssueDetails.0.stage_when_issued')
                        ->label('Stage Saat Diambil')
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(fn ($state) => $state ?: 'Default')
                        ->placeholder('Default'),

                    TextColumn::make('departemen')
                        ->label('Departemen')
                        ->icon('heroicon-m-building-office-2')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('bagian')
                        ->label('Bagian')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),

                ColumnGroup::make('Log Sistem', [
                    TextColumn::make('createdBy.name')
                        ->label('Dibuat Oleh')
                        ->icon(Heroicon::User)
                        ->color('gray')
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('created_at')
                        ->label('Tgl Dibuat')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('updated_at')
                        ->label('Tgl Diperbarui')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('gray')
                        ->slideOver(),
                    Action::make('cetak_mir')
                        ->label('Cetak')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn (MaterialIssue $record): string => route('filament.admin.resources.material-issues.print', $record))
                        ->openUrlInNewTab(),
                    EditAction::make()
                        ->color('info')
                        ->slideOver(),
                    DeleteAction::make()
                        ->requiresConfirmation(),
                ])
                    ->label('')
                    ->icon(Heroicon::EllipsisHorizontal)
                    ->size(Size::Small)
                    ->color('info')
                    ->outlined()
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('cetak_mir_bulk')
                        ->label('Cetak MIR Terpilih')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->implode(',');

                            return redirect()->route('filament.admin.resources.material-issues.print_bulk', ['ids' => $ids]);
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada Material Issues (MIR)')
            ->emptyStateDescription('Buat catatan pengambilan barang baru melalui form MIR.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->defaultSort('id', 'desc');
    }
}
