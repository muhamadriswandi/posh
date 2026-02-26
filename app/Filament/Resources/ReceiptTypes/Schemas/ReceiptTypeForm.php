<?php

namespace App\Filament\Resources\ReceiptTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReceiptTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('group')
                    ->options([
                        'Pajak Daerah' => 'Pajak Daerah',
                        'Retribusi Daerah' => 'Retribusi Daerah',
                        'Lain-lain PAD yang sah' => 'Lain-lain PAD yang sah',
                    ])
                    ->required(),
                TextInput::make('code')
                    ->maxLength(50),
                \Filament\Forms\Components\TagsInput::make('keywords')
                    ->placeholder('Add keywords for auto-detection'),
            ]);
    }
}
