<?php

namespace App\Filament\Resources\ReceiptTypes;

use App\Filament\Resources\ReceiptTypes\Pages\CreateReceiptType;
use App\Filament\Resources\ReceiptTypes\Pages\EditReceiptType;
use App\Filament\Resources\ReceiptTypes\Pages\ListReceiptTypes;
use App\Filament\Resources\ReceiptTypes\Pages\ViewReceiptType;
use App\Filament\Resources\ReceiptTypes\Schemas\ReceiptTypeForm;
use App\Filament\Resources\ReceiptTypes\Schemas\ReceiptTypeInfolist;
use App\Filament\Resources\ReceiptTypes\Tables\ReceiptTypesTable;
use App\Models\ReceiptType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReceiptTypeResource extends Resource
{
    protected static ?string $model = ReceiptType::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ReceiptTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReceiptTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceiptTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceiptTypes::route('/'),
            'create' => CreateReceiptType::route('/create'),
            'view' => ViewReceiptType::route('/{record}'),
            'edit' => EditReceiptType::route('/{record}/edit'),
        ];
    }
}
