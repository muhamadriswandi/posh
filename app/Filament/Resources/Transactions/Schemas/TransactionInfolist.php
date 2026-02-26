<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('bank_account_id')
                    ->numeric(),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('description'),
                TextEntry::make('amount')
                    ->money('IDR'),
                \Filament\Infolists\Components\RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('description'),
                        TextEntry::make('amount')
                            ->money('IDR'),
                        TextEntry::make('receiptType.name')
                            ->badge()
                            ->color('info')
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('payment_channel_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('reference')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
