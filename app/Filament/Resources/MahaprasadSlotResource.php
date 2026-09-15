<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MahaprasadSlotResource\Pages;
use App\Models\MahaprasadSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MahaprasadSlotResource extends Resource
{
    protected static ?string $model = MahaprasadSlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationGroup = 'Events & Content';

    protected static ?string $navigationLabel = 'Mahaprasad Sponsors';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('What they are sponsoring')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('submitted_by')
                    ->label('Submitted by (optional)')
                    ->relationship('submitter', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending')
                    ->helperText('Only one slot per date can be approved — approving a second slot for an already-approved date will fail.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('D, j M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
                Tables\Columns\TextColumn::make('submitter.name')
                    ->label('Submitted By')
                    ->placeholder('— added by admin —'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (MahaprasadSlot $record) => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (MahaprasadSlot $record) {
                        if (MahaprasadSlot::where('date', $record->date)->where('status', 'approved')->where('id', '!=', $record->id)->exists()) {
                            Notification::make()
                                ->title('That date is already sponsored by another approved slot.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update(['status' => 'approved']);

                        Notification::make()->title('Mahaprasad slot approved.')->success()->send();
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
            'index' => Pages\ListMahaprasadSlots::route('/'),
            'create' => Pages\CreateMahaprasadSlot::route('/create'),
            'edit' => Pages\EditMahaprasadSlot::route('/{record}/edit'),
        ];
    }
}
