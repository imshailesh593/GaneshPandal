<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AartiSlotResource\Pages;
use App\Models\AartiSlot;
use Filament\Forms;
use Filament\Forms\Form;
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
                Tables\Columns\TextColumn::make('activeBooking.member.family.name')
                    ->label('Booked By')
                    ->placeholder('— available —'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
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
