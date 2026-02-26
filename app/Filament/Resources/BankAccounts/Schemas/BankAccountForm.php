<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('bank_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('account_number')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('account_holder')
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
