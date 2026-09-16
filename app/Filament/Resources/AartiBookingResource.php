<?php

namespace App\Filament\Resources;

use App\Filament\Exports\AartiBookingExporter;
use App\Filament\Resources\AartiBookingResource\Pages;
use App\Models\AartiBooking;
use App\Models\AartiSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
                Forms\Components\Select::make('aarti_slot_id')
                    ->label('Date')
                    ->relationship('aartiSlot', 'date', modifyQueryUsing: fn ($query) => $query->orderBy('date'))
                    ->getOptionLabelFromRecordUsing(fn (AartiSlot $record) => $record->date->format('D, j M Y').($record->is_active ? '' : ' (blocked)'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabledOn('edit')
                    ->rules([
                        fn (?Model $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($record) {
                            $exists = AartiBooking::where('aarti_slot_id', $value)
                                ->whereIn('status', ['pending', 'confirmed'])
                                ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($exists) {
                                $fail('This date already has an active booking.');
                            }
                        },
                    ]),
                Forms\Components\Select::make('member_id')
                    ->label('Member (optional)')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Leave blank and fill in the name below for a guest or the mandal chief.'),
                Forms\Components\TextInput::make('guest_name')
                    ->label('Guest / chief name (if not a member)')
                    ->maxLength(255)
                    ->requiredWithout('member_id'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending confirmation',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('confirmed')
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
                Tables\Columns\TextColumn::make('plot')
                    ->label('Plot / Guest')
                    ->getStateUsing(fn (AartiBooking $record) => $record->member?->family?->plot_number ?? $record->guest_name ?? '—'),
                Tables\Columns\TextColumn::make('booked_for')
                    ->label('Booked For')
                    ->getStateUsing(fn (AartiBooking $record) => $record->displayName()),
                Tables\Columns\TextColumn::make('member.mobile')
                    ->label('Mobile')
                    ->placeholder('—'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
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
                        'pending' => 'Pending confirmation',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Book on behalf of someone'),
                Tables\Actions\ExportAction::make()
                    ->exporter(AartiBookingExporter::class),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AartiBooking $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (AartiBooking $record) {
                        $record->update(['status' => 'confirmed']);

                        Notification::make()->title('Booking confirmed.')->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (AartiBooking $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalDescription('This frees the date up for other families to book.')
                    ->action(function (AartiBooking $record) {
                        $record->update(['status' => 'cancelled']);

                        Notification::make()->title('Booking rejected — the date is open again.')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(AartiBookingExporter::class),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAartiBookings::route('/'),
            'create' => Pages\CreateAartiBooking::route('/create'),
            'edit' => Pages\EditAartiBooking::route('/{record}/edit'),
        ];
    }
}
