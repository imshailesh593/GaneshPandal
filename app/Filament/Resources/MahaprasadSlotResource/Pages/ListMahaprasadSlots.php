<?php

namespace App\Filament\Resources\MahaprasadSlotResource\Pages;

use App\Filament\Resources\MahaprasadSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMahaprasadSlots extends ListRecords
{
    protected static string $resource = MahaprasadSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
