<?php

namespace App\Filament\Exports;

use App\Models\Transaction;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('date')
                ->label('Tanggal'),
            ExportColumn::make('bankAccount.name')
                ->label('Account'),
            ExportColumn::make('description'),
            ExportColumn::make('items_summary')
                ->label('Items Detail')
                ->getStateUsing(function (Transaction $record): string {
                    return $record->items->map(function ($item) {
                        $type = $item->receiptType?->name ? " [{$item->receiptType->name}]" : "";
                        $amount = number_format($item->amount, 0, ',', '.');
                        return "{$item->description}{$type} (Rp {$amount})";
                    })->join('; ');
                }),
            ExportColumn::make('amount')
                ->label('Nominal')
                ->getStateUsing(fn($record) => number_format($record->amount, 0, ',', '.')),
            ExportColumn::make('type')
                ->label('Tipe'),
            ExportColumn::make('paymentChannel.name')
                ->label('Payment Channel'),
            ExportColumn::make('reference'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaction export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
