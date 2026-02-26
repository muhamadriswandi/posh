<?php

namespace App\Filament\Resources\ReceiptTypes\Pages;

use App\Filament\Resources\ReceiptTypes\ReceiptTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReceiptType extends CreateRecord
{
    protected static string $resource = ReceiptTypeResource::class;
}
