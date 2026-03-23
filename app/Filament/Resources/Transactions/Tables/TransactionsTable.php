<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Transaction;
use App\Models\ReceiptType;
use App\Models\PaymentChannel;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use App\Filament\Imports\TransactionImporter;
use App\Filament\Exports\TransactionExporter;
use Filament\Actions\DeleteAction;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('bankAccount.name')
                    ->searchable()
                    ->sortable()
                    ->label('Account'),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'credit' => 'success',
                        'debit' => 'danger'
                    }),
                TextColumn::make('paymentChannel.name')
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),
                TextColumn::make('items.nama_opd')
                    ->label('OPD')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->searchable(),
                TextColumn::make('reference')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(['debit' => 'Debit', 'credit' => 'Credit']),
                SelectFilter::make('bank_account')
                    ->relationship('bankAccount', 'name'),
                Filter::make('date')
                    ->schema([
                        DatePicker::make('date_from'),
                        DatePicker::make('date_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn($query, $date) => $query->whereDate('date', '>=', $date))
                            ->when($data['date_until'], fn($query, $date) => $query->whereDate('date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                Action::make('auto_categorize')
                    ->label('Auto Categorize Now')
                    ->color('primary')
                    ->action(function () {
                        $transactions = Transaction::with('items')->get();
                        $receiptTypes = ReceiptType::all();
                        $paymentChannels = PaymentChannel::all();

                        foreach ($transactions as $transaction) {
                            // Categorize channel on transaction level
                            $descMain = strtolower($transaction->description);
                            foreach ($paymentChannels as $channel) {
                                if ($channel->keywords) {
                                    foreach ($channel->keywords as $keyword) {
                                        if (str_contains($descMain, strtolower($keyword))) {
                                            $transaction->payment_channel_id = $channel->id;
                                            break;
                                        }
                                    }
                                }
                            }
                            if ($transaction->isDirty('payment_channel_id')) {
                                $transaction->save();
                            }

                            // Categorize receipt type on item level
                            foreach ($transaction->items as $item) {
                                if ($item->receipt_type_id) {
                                    continue;
                                }
                                $descItem = strtolower($item->description);
                                foreach ($receiptTypes as $type) {
                                    if ($type->keywords) {
                                        foreach ($type->keywords as $keyword) {
                                            if (str_contains($descItem, strtolower($keyword))) {
                                                $item->receipt_type_id = $type->id;
                                                $item->save();
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        Notification::make()->title('Auto-categorization completed')->success()->send();
                    }),
                ImportAction::make()
                    ->importer(TransactionImporter::class)
                    ->csvDelimiter(';'),
                ExportAction::make()
                    ->exporter(TransactionExporter::class)
                    ->csvDelimiter(';')
                    ->formats([
                        \Filament\Actions\Exports\Enums\ExportFormat::Xlsx,
                        \Filament\Actions\Exports\Enums\ExportFormat::Csv,
                    ]),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('assign_receipt_type')
                        ->schema([
                            \Filament\Forms\Components\Select::make('receipt_type_id')
                                ->label('Receipt Type')
                                ->options(ReceiptType::pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($transaction) use ($data) {
                                $transaction->items()->update(['receipt_type_id' => $data['receipt_type_id']]);
                            });
                        }),
                    BulkAction::make('assign_payment_channel')
                        ->schema([
                            \Filament\Forms\Components\Select::make('payment_channel_id')
                                ->relationship('paymentChannel', 'name')
                                ->required(),
                        ])
                        ->action(fn(Collection $records, array $data) => $records->each->update(['payment_channel_id' => $data['payment_channel_id']])),
                    BulkAction::make('assign_opd')
                        ->schema([
                            \Filament\Forms\Components\Select::make('opd_id')
                                ->label('OPD')
                                ->options(\App\Models\Opd::pluck('nama_opd', 'id'))
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $opd = \App\Models\Opd::find($data['opd_id']);
                            $records->each(function ($transaction) use ($data, $opd) {
                                $transaction->items()->update([
                                    'opd_id' => $data['opd_id'],
                                    'nama_opd' => $opd ? $opd->nama_opd : null,
                                ]);
                            });
                        }),
                ]),
            ]);
    }
}
