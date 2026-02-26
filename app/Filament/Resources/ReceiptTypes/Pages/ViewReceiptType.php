<?php

namespace App\Filament\Resources\ReceiptTypes\Pages;

use App\Filament\Resources\ReceiptTypes\ReceiptTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReceiptType extends ViewRecord
{
    protected static string $resource = ReceiptTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
