<?php

namespace App\Filament\Resources\GameResource\RelationManagers;

use App\Enums\AgeGroup;
use App\Enums\Gender;
use App\Enums\Position;
use App\Filament\Pages\EnterWinners;
use App\Models\GameParticipant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    protected static ?string $title = 'Winners';

    protected static ?string $modelLabel = 'winner';

    protected static ?string $pluralModelLabel = 'winners';

    protected static ?string $recordTitleAttribute = 'participant_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('age_group')
                    ->label('Group')
                    ->options(AgeGroup::class)
                    ->required()
                    ->live(),
                Forms\Components\Select::make('gender')
                    ->options(Gender::class)
                    ->required()
                    ->live(),
                Forms\Components\Select::make('position')
                    ->label('Rank')
                    ->options(Position::class)
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                            ->where('game_id', $this->getOwnerRecord()->getKey())
                            ->where('age_group', $get('age_group'))
                            ->where('gender', $get('gender')),
                    )
                    ->validationMessages(['unique' => 'That rank already has a winner in this group and gender.']),
                Forms\Components\Select::make('member_id')
                    ->label('Member (optional)')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Leave blank and type the name below for someone who is not a registered member.'),
                Forms\Components\TextInput::make('participant_name')
                    ->label('Name (if not a member)')
                    ->maxLength(255)
                    ->requiredWithout('member_id')
                    ->helperText('This name is printed on the certificate.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->orderByRaw("FIELD(age_group, 'small', 'medium', 'large')")
                ->orderByRaw("FIELD(gender, 'male', 'female')")
                ->orderByRaw("FIELD(position, 'first', 'second', 'third')"))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('age_group')
                    ->label('Group')
                    ->badge()
                    ->formatStateUsing(fn (AgeGroup $state): string => $state->marathi())
                    ->color('gray'),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (Gender $state, GameParticipant $record): string => $record->age_group->genderLabel($state))
                    ->color('gray'),
                Tables\Columns\TextColumn::make('position')
                    ->label('Rank')
                    ->badge()
                    ->formatStateUsing(fn (Position $state): string => match ($state) {
                        Position::First => '🥇 1st ('.$state->formal().')',
                        Position::Second => '🥈 2nd ('.$state->formal().')',
                        Position::Third => '🥉 3rd ('.$state->formal().')',
                    })
                    ->color(fn (Position $state): string => match ($state) {
                        Position::First => 'warning',
                        Position::Second => 'gray',
                        Position::Third => 'danger',
                    }),
                Tables\Columns\TextColumn::make('winner')
                    ->label('Winner')
                    ->weight('medium')
                    ->wrap()
                    ->getStateUsing(fn (GameParticipant $record) => $record->displayName()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('age_group')
                    ->label('Group')
                    ->options(AgeGroup::class),
                Tables\Filters\SelectFilter::make('gender')
                    ->options(Gender::class),
            ])
            ->headerActions([
                $this->getEnterWinnersAction(),
                Tables\Actions\CreateAction::make('addSingle')
                    ->label('Add single winner')
                    ->modalHeading('Add single winner')
                    ->icon('heroicon-o-plus')
                    ->color('gray')
                    ->labeledFrom('sm'),
                Tables\Actions\Action::make('allCertificates')
                    ->label('All certificates (PDF)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn () => $this->getOwnerRecord()->participants()->exists())
                    ->url(fn () => route('filament.admin.certificates.game', $this->getOwnerRecord()))
                    ->openUrlInNewTab()
                    ->labeledFrom('md'),
            ])
            ->emptyStateActions([
                $this->getEnterWinnersAction(),
            ])
            ->actions([
                Tables\Actions\Action::make('certificate')
                    ->label('Certificate')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->iconButton()
                    ->tooltip('Download certificate (PDF)')
                    ->url(fn (GameParticipant $record) => route('filament.admin.certificates.winner', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit winner'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete winner'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getEnterWinnersAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('enterWinners')
            ->label('Add winners')
            ->icon('heroicon-o-trophy')
            ->color('warning')
            ->button()
            ->visible(fn (): bool => EnterWinners::canAccess())
            ->url(fn (): string => EnterWinners::getUrl(['game' => $this->getOwnerRecord()->getKey()]));
    }
}
