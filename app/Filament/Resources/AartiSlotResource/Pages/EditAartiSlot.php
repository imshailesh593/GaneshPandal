<?php

namespace App\Filament\Resources\AartiSlotResource\Pages;

use App\Filament\Resources\AartiSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAartiSlot extends EditRecord
{
    protected static string $resource = AartiSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
