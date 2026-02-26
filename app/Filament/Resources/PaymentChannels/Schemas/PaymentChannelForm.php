<?php

namespace App\Filament\Resources\PaymentChannels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentChannelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('code')
                    ->maxLength(50),
                \Filament\Forms\Components\TagsInput::make('keywords')
                    ->placeholder('Add keywords for auto-detection'),
            ]);
    }
}
