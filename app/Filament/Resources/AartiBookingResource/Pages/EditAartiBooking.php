<?php

namespace App\Filament\Resources\AartiBookingResource\Pages;

use App\Filament\Resources\AartiBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAartiBooking extends EditRecord
{
    protected static string $resource = AartiBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
