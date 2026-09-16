<?php

namespace App\Filament\Exports;

use App\Models\AartiBooking;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AartiBookingExporter extends Exporter
{
    protected static ?string $model = AartiBooking::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('aartiSlot.date')
                ->label('Date'),
            ExportColumn::make('member.family.plot_number')
                ->label('Plot'),
            ExportColumn::make('member.name')
                ->label('Member'),
            ExportColumn::make('guest_name')
                ->label('Guest / Chief'),
            ExportColumn::make('member.mobile')
                ->label('Mobile'),
            ExportColumn::make('status')
                ->label('Status'),
            ExportColumn::make('created_at')
                ->label('Booked At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your aarti bookings export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
