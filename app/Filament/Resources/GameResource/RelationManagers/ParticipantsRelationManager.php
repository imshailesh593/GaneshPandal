<?php

namespace App\Filament\Resources\GameResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('member_id')
                    ->label('Member (optional)')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Leave blank and use the name field below for a non-member participant.'),
                Forms\Components\TextInput::make('participant_name')
                    ->label('Name (if not a member)')
                    ->maxLength(255),
                Forms\Components\Select::make('position')
                    ->options([
                        'first' => '1st place',
                        'second' => '2nd place',
                        'third' => '3rd place',
                        'participation' => 'Participation',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('participant_name')
            ->columns([
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Name')
                    ->getStateUsing(fn ($record) => $record->displayName()),
                Tables\Columns\BadgeColumn::make('position')
                    ->color(fn (string $state): string => match ($state) {
                        'first' => 'warning',
                        'second', 'third' => 'gray',
                        default => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
