<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\ReceiptType;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\RawJs;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bank_account_id')
                    ->relationship('bankAccount', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('date')
                    ->required(),
                Select::make('type')
                    ->options([
                        'debit' => 'Debit',
                        'credit' => 'Credit',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->prefix('Rp')
                    ->type('text')
                    ->extraInputAttributes([
                        'oninput' => 'this.value = this.value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")',
                    ])
                    ->formatStateUsing(fn($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                    ->dehydrateStateUsing(fn($state) => str_replace('.', '', $state))
                    ->rules([
                        fn(Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                            $items = $get('items') ?? [];
                            $total = 0;
                            $cleanValue = (float) str_replace('.', '', $value);
                            foreach ($items as $item) {
                                $total += (float) str_replace('.', '', ($item['amount'] ?? 0));
                            }

                            if (abs($cleanValue - $total) > 0.01) {
                                $fail("Total nominal rincian (Rp " . number_format($total, 2, ',', '.') . ") harus sama dengan nominal transaksi.");
                            }
                        },
                    ]),
                \Filament\Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                \Filament\Forms\Components\Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        TextInput::make('description')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('amount')
                            ->required()
                            ->prefix('Rp')
                            ->type('text')
                            ->extraInputAttributes([
                                'oninput' => 'this.value = this.value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")',
                            ])
                            ->formatStateUsing(fn($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn($state) => str_replace('.', '', $state)),
                        Select::make('receipt_type_id')
                            ->relationship('receiptType', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Receipt Type'),
                    ])
                    ->columnSpanFull()
                    ->columns(4),
                Select::make('payment_channel_id')
                    ->relationship('paymentChannel', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select Payment Channel (Optional)'),
                TextInput::make('reference'),
            ]);
    }
}
