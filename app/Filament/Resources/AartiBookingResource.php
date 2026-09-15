<?php

namespace App\Filament\Resources;

use App\Filament\Exports\AartiBookingExporter;
use App\Filament\Resources\AartiBookingResource\Pages;
use App\Models\AartiBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AartiBookingResource extends Resource
{
    protected static ?string $model = AartiBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Aarti Bookings';

    protected static ?string $navigationGroup = 'Aarti Booking';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'booked' => 'Booked',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('aarti_slot_id')
            ->columns([
                Tables\Columns\TextColumn::make('aartiSlot.date')
                    ->label('Date')
                    ->date('D, j M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.family.name')
                    ->label('Family')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Member')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.mobile')
                    ->label('Mobile')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'booked',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Booked At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'booked' => 'Booked',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(AartiBookingExporter::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(AartiBookingExporter::class),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAartiBookings::route('/'),
            'edit' => Pages\EditAartiBooking::route('/{record}/edit'),
        ];
    }
}
