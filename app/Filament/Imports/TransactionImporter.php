<?php

namespace App\Filament\Imports;

use App\Models\Transaction;
use App\Models\BankAccount;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransactionImporter extends Importer
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('date')
                ->label('Tanggal')
                ->guess(['date', 'tanggal', 'tgl']),

            ImportColumn::make('description')
                ->label('Diskripsi')
                ->guess(['description', 'desc', 'keterangan', 'ket']),

            ImportColumn::make('amount')
                ->label('Jumlah')
                ->guess(['amount', 'nominal', 'nilai', 'jumlah']),

            ImportColumn::make('type')
                ->label('Tipe')
                ->guess(['type', 'tipe', 'jenis']),

            ImportColumn::make('bank_account_id')
                ->label('Bank Account')
                ->guess(['bank_account_id', 'bankaccountid', 'bank', 'rekening', 'idrekening']),

            ImportColumn::make('reference')
                ->label('Referensi')
                ->guess(['reference', 'ref', 'no', 'nomor', 'referensi']),
        ];
    }

    public function resolveRecord(): ?Transaction
    {

        $record = Transaction::firstOrNew([
            'reference' => $this->data['reference'] ?? null,
        ]);

        return $record;
    }

    protected function beforeValidate(): void
    {
        if (!empty($this->data['date'])) {
            $this->data['date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $this->data['date'])
                ->format('Y-m-d');
        }

        if (!empty($this->data['amount'])) {
            $this->data['amount'] = (float) str_replace(',', '.', str_replace('.', '', $this->data['amount']));
        }

        if (!empty($this->data['bank_account_id'])) {
            $this->data['bank_account_id'] = (int) $this->data['bank_account_id'];
        }
    }

    protected function afterSave(): void
    {
        // Auto-create item only if it doesn't exist (prevents duplicates in upsert)
        if ($this->record->items()->count() === 0) {
            $this->record->items()->create([
                'description' => $this->record->description,
                'amount' => $this->record->amount,
            ]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your transaction import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
