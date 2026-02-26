<?php

namespace App\Filament\Resources\ReceiptTypes\Pages;

use App\Filament\Resources\ReceiptTypes\ReceiptTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReceiptType extends EditRecord
{
    protected static string $resource = ReceiptTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
