<?php

namespace App\Filament\Resources\PaymentChannels;

use App\Filament\Resources\PaymentChannels\Pages\CreatePaymentChannel;
use App\Filament\Resources\PaymentChannels\Pages\EditPaymentChannel;
use App\Filament\Resources\PaymentChannels\Pages\ListPaymentChannels;
use App\Filament\Resources\PaymentChannels\Pages\ViewPaymentChannel;
use App\Filament\Resources\PaymentChannels\Schemas\PaymentChannelForm;
use App\Filament\Resources\PaymentChannels\Schemas\PaymentChannelInfolist;
use App\Filament\Resources\PaymentChannels\Tables\PaymentChannelsTable;
use App\Models\PaymentChannel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentChannelResource extends Resource
{
    protected static ?string $model = PaymentChannel::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PaymentChannelForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentChannelInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentChannelsTable::configure($table);
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
            'index' => ListPaymentChannels::route('/'),
            'create' => CreatePaymentChannel::route('/create'),
            'view' => ViewPaymentChannel::route('/{record}'),
            'edit' => EditPaymentChannel::route('/{record}/edit'),
        ];
    }
}
