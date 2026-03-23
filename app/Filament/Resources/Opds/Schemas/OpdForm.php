<?php

namespace App\Filament\Resources\Opds\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OpdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_opd')
                    ->required(),
                TextInput::make('singkatan'),
            ]);
    }
}
