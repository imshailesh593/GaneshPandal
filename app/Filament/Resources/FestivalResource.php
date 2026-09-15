<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FestivalResource\Pages;
use App\Models\Festival;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FestivalResource extends Resource
{
    protected static ?string $model = Festival::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Aarti Booking';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('sthapana_date')
                    ->label('Sthapana date')
                    ->required(),
                Forms\Components\DatePicker::make('visarjan_date')
                    ->label('Visarjan date')
                    ->required()
                    ->afterOrEqual('sthapana_date'),
                Forms\Components\TimePicker::make('aarti_time')
                    ->required()
                    ->seconds(false),
                Forms\Components\Toggle::make('is_active')
                    ->helperText('Only one festival can be active at a time. The public booking page shows the active festival.')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sthapana_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('visarjan_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aarti_time')
                    ->time('g:i A'),
                Tables\Columns\TextColumn::make('aarti_slots_count')
                    ->label('Slots')
                    ->counts('aartiSlots'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('generateSlots')
                    ->label('Generate Aarti Slots')
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Creates any missing daily aarti slots between the sthapana and visarjan dates. Existing slots (including booked ones) are left untouched.')
                    ->action(function (Festival $record) {
                        $created = $record->generateAartiSlots();

                        Notification::make()
                            ->title($created > 0 ? "Created {$created} aarti slot(s)." : 'All aarti slots already exist.')
                            ->success()
                            ->send();
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
            'index' => Pages\ListFestivals::route('/'),
            'create' => Pages\CreateFestival::route('/create'),
            'edit' => Pages\EditFestival::route('/{record}/edit'),
        ];
    }
}
