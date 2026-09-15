<?php

namespace App\Filament\Resources\AartiSlotResource\Pages;

use App\Filament\Resources\AartiSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAartiSlots extends ListRecords
{
    protected static string $resource = AartiSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
