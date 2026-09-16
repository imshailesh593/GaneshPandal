<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AartiSlotResource\Pages;
use App\Models\AartiSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AartiSlotResource extends Resource
{
    protected static ?string $model = AartiSlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Aarti Booking';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('festival_id')
                    ->relationship('festival', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TimePicker::make('time')
                    ->required()
                    ->seconds(false),
                Forms\Components\Toggle::make('is_active')
                    ->helperText('Turn off to hide this date from booking without deleting it.')
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->label('Note (shown to members when blocked)')
                    ->helperText('E.g. "Pooja rescheduled" or "Reserved for the mandal committee".')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date')
            ->columns([
                Tables\Columns\TextColumn::make('festival.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->date('D, j M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->time('g:i A'),
                Tables\Columns\TextColumn::make('booked_by')
                    ->label('Booked By')
                    ->getStateUsing(fn (AartiSlot $record) => $record->activeBooking?->bookedByLabel())
                    ->placeholder('— available —'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('note')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('festival_id')
                    ->label('Festival')
                    ->relationship('festival', 'name'),
                Tables\Filters\TernaryFilter::make('booked')
                    ->label('Booking status')
                    ->placeholder('All')
                    ->trueLabel('Booked')
                    ->falseLabel('Available')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('activeBooking'),
                        false: fn (Builder $query) => $query->whereDoesntHave('activeBooking'),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('block')
                    ->label('Block')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (AartiSlot $record) => $record->is_active)
                    ->requiresConfirmation()
                    ->modalDescription('Members will no longer be able to book this date. Any existing booking on it is not affected — cancel it separately from Aarti Bookings if needed.')
                    ->form([
                        Forms\Components\Textarea::make('note')
                            ->label('Reason (shown to members)')
                            ->required(),
                    ])
                    ->action(function (AartiSlot $record, array $data) {
                        $record->update(['is_active' => false, 'note' => $data['note']]);

                        Notification::make()->title('Date blocked.')->success()->send();
                    }),
                Tables\Actions\Action::make('unblock')
                    ->label('Unblock')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AartiSlot $record) => ! $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (AartiSlot $record) {
                        $record->update(['is_active' => true, 'note' => null]);

                        Notification::make()->title('Date unblocked.')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAartiSlots::route('/'),
            'create' => Pages\CreateAartiSlot::route('/create'),
            'edit' => Pages\EditAartiSlot::route('/{record}/edit'),
        ];
    }
}
