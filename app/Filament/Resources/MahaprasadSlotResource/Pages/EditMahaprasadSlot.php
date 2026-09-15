<?php

namespace App\Filament\Resources\MahaprasadSlotResource\Pages;

use App\Filament\Resources\MahaprasadSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMahaprasadSlot extends EditRecord
{
    protected static string $resource = MahaprasadSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
