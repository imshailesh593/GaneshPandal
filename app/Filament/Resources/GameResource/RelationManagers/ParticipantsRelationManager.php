<?php

namespace App\Filament\Resources\GameResource\RelationManagers;

use App\Enums\AgeGroup;
use App\Enums\Gender;
use App\Enums\Position;
use App\Models\GameParticipant;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
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

    protected static ?array $memberOptionsCache = null;

    protected function getMemberOptions(): array
    {
        if (static::$memberOptionsCache !== null) {
            return static::$memberOptionsCache;
        }

        return static::$memberOptionsCache = Member::with('family')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (Member $member) {
                $label = $member->name;
                if ($member->family?->plot_number) {
                    $label .= " (Plot {$member->family->plot_number})";
                }

                return [$member->id => $label];
            })
            ->toArray();
    }

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
            ->modalHeading('Add / Manage Winners')
            ->modalDescription('Quickly enter 1st, 2nd, and 3rd place winners for male and female categories across age groups.')
            ->modalSubmitActionLabel('Save winners')
            ->modalWidth(MaxWidth::FiveExtraLarge)
            ->fillForm(function (): array {
                $game = $this->getOwnerRecord();
                $data = [];

                foreach ($game->participants()->get() as $participant) {
                    $group = $participant->age_group->value;
                    $gender = $participant->gender->value;
                    $pos = $participant->position->value;

                    $data["{$group}_{$gender}_{$pos}_member_id"] = $participant->member_id;
                    $data["{$group}_{$gender}_{$pos}_name"] = $participant->participant_name;
                }

                return $data;
            })
            ->form([
                Forms\Components\Tabs::make('AgeGroups')
                    ->tabs(
                        collect(AgeGroup::cases())->map(function (AgeGroup $group) {
                            $tabLabel = match ($group) {
                                AgeGroup::Small => 'लहान गट (Small)',
                                AgeGroup::Medium => 'मध्यम गट (Medium)',
                                AgeGroup::Large => 'मोठा गट (Large)',
                            };

                            return Forms\Components\Tabs\Tab::make($group->value)
                                ->label($tabLabel)
                                ->badge(function () use ($group): ?string {
                                    $count = $this->getOwnerRecord()->participants()->where('age_group', $group)->count();

                                    return $count > 0 ? (string) $count : null;
                                })
                                ->badgeColor('success')
                                ->schema([
                                    $this->buildGenderSection($group, Gender::Male),
                                    $this->buildGenderSection($group, Gender::Female),
                                ]);
                        })->all()
                    ),
            ])
            ->action(function (array $data): void {
                $game = $this->getOwnerRecord();

                foreach (AgeGroup::cases() as $group) {
                    foreach (Gender::cases() as $gender) {
                        foreach (Position::cases() as $pos) {
                            $memberId = $data["{$group->value}_{$gender->value}_{$pos->value}_member_id"] ?? null;
                            $name = trim($data["{$group->value}_{$gender->value}_{$pos->value}_name"] ?? '');

                            if ($memberId || $name !== '') {
                                GameParticipant::updateOrCreate(
                                    [
                                        'game_id' => $game->id,
                                        'age_group' => $group,
                                        'gender' => $gender,
                                        'position' => $pos,
                                    ],
                                    [
                                        'member_id' => $memberId ?: null,
                                        'participant_name' => $memberId ? null : ($name !== '' ? $name : null),
                                    ]
                                );
                            } else {
                                $game->participants()
                                    ->where('age_group', $group)
                                    ->where('gender', $gender)
                                    ->where('position', $pos)
                                    ->delete();
                            }
                        }
                    }
                }

                Notification::make()
                    ->title('Winners saved successfully')
                    ->success()
                    ->send();
            });
    }

    protected function buildGenderSection(AgeGroup $group, Gender $gender): Forms\Components\Section
    {
        $genderTitle = match ($gender) {
            Gender::Male => 'Male — '.$group->genderLabel(Gender::Male),
            Gender::Female => 'Female — '.$group->genderLabel(Gender::Female),
        };

        return Forms\Components\Section::make($genderTitle)
            ->icon('heroicon-m-trophy')
            ->collapsible()
            ->compact()
            ->schema([
                Forms\Components\Grid::make([
                    'default' => 1,
                ])->schema([
                    $this->buildPositionFieldset($group, $gender, Position::First),
                    $this->buildPositionFieldset($group, $gender, Position::Second),
                    $this->buildPositionFieldset($group, $gender, Position::Third),
                ]),
            ]);
    }

    protected function buildPositionFieldset(AgeGroup $group, Gender $gender, Position $pos): Forms\Components\Fieldset
    {
        $medal = match ($pos) {
            Position::First => '🥇 1st Place ('.$pos->formal().' क्रमांक)',
            Position::Second => '🥈 2nd Place ('.$pos->formal().' क्रमांक)',
            Position::Third => '🥉 3rd Place ('.$pos->formal().' क्रमांक)',
        };

        $key = "{$group->value}_{$gender->value}_{$pos->value}";

        return Forms\Components\Fieldset::make($medal)
            ->schema([
                Forms\Components\Select::make("{$key}_member_id")
                    ->label('Member (optional)')
                    ->placeholder('Search registered member...')
                    ->options(fn (): array => $this->getMemberOptions())
                    ->searchable()
                    ->preload()
                    ->columnSpan([
                        'default' => 12,
                        'md' => 6,
                    ]),
                Forms\Components\TextInput::make("{$key}_name")
                    ->label('Or Name (if not a member)')
                    ->placeholder('Type participant name...')
                    ->maxLength(255)
                    ->columnSpan([
                        'default' => 12,
                        'sm' => 6,
                    ]),
            ])
            ->columns(12);
    }
}
