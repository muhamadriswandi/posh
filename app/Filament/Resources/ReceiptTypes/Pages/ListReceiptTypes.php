<?php

namespace App\Filament\Resources\ReceiptTypes\Pages;

use App\Filament\Resources\ReceiptTypes\ReceiptTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReceiptTypes extends ListRecords
{
    protected static string $resource = ReceiptTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
