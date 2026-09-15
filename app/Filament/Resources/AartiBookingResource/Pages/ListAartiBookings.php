<?php

namespace App\Filament\Resources\AartiBookingResource\Pages;

use App\Filament\Resources\AartiBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAartiBookings extends ListRecords
{
    protected static string $resource = AartiBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
