<?php

namespace App\Filament\Imports;

use App\Models\Transaction;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;

class TransactionImporter extends Importer
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('date')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('description')
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('amount')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required', 'in:debit,credit']),
            ImportColumn::make('bank_account_id')
                ->relationship(resolveUsing: 'account_number')
                ->rules(['required', 'exists:bank_accounts,account_number']),
            ImportColumn::make('reference'),
        ];
    }

    public function resolveRecord(): ?Transaction
    {
        return new Transaction();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your transaction import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
