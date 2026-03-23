<?php

namespace App\Filament\Resources\ReceiptTypes\Pages;

use App\Filament\Resources\ReceiptTypes\ReceiptTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use App\Models\ReceiptType;
use Filament\Schemas\Components\Tabs\Tab as TabsTab;

class ListReceiptTypes extends ListRecords
{
    protected static string $resource = ReceiptTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
    $tabs = [
        'all' => TabsTab::make('Semua')
        ->badge(ReceiptType::count()),
    ];

    $groups = ReceiptType::query()
        ->select('group')
        ->distinct()
        ->orderByRaw("CASE 
            WHEN `group` = 'Pajak Daerah' THEN 1 
            WHEN `group` = 'Retribusi Daerah' THEN 2 
            WHEN `group` = 'Lain-lain PAD yang sah' THEN 3 
            ELSE 4 END")
        ->pluck('group');

    foreach ($groups as $group) {
        $tabs[$group] = TabsTab::make($group)
            ->modifyQueryUsing(fn ($query) =>
                $query->where('group', $group)
            )
            ->badge(ReceiptType::where('group', $group)->count());
        }

    return $tabs;
    
    }
}
