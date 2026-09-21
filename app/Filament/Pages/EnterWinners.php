<?php

namespace App\Filament\Pages;

use App\Enums\AgeGroup;
use App\Enums\Gender;
use App\Enums\Position;
use App\Models\Game;
use App\Models\GameParticipant;
use App\Models\Member;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class EnterWinners extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Events & Content';

    protected static ?string $navigationLabel = 'Enter Winners';

    protected static ?string $title = 'Enter Winners';

    protected static string $view = 'filament.pages.enter-winners';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('update_game') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'game_id' => Game::query()->whereKey(request()->integer('game'))->value('id'),
            ...$this->blankWinners(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Select::make('game_id')
                    ->label('Step 1 — Game')
                    ->placeholder('Choose a game')
                    ->options(fn (): array => Game::query()->orderByDesc('id')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadCategory(resetCategory: true)),
                ToggleButtons::make('gender')
                    ->label('Step 2 — Male or female')
                    ->options(Gender::class)
                    ->inline()
                    ->required()
                    ->live()
                    ->visible(fn (Get $get): bool => filled($get('game_id')))
                    ->afterStateUpdated(fn () => $this->loadCategory()),
                ToggleButtons::make('age_group')
                    ->label('Step 3 — Group')
                    ->options(collect(AgeGroup::cases())->mapWithKeys(
                        fn (AgeGroup $group): array => [$group->value => $group->marathi().' ('.ucfirst($group->value).')']
                    )->all())
                    ->inline()
                    ->required()
                    ->live()
                    ->visible(fn (Get $get): bool => filled($get('game_id')) && filled($get('gender')))
                    ->afterStateUpdated(fn () => $this->loadCategory()),
                Section::make(fn (Get $get): string => 'Step 4 — Winners: '.$this->categoryTitle($get('gender'), $get('age_group')))
                    ->description('Pick a registered member, or type a name for someone who is not one. Leave a rank empty if it has no winner yet. Saving only touches this group.')
                    ->visible(fn (Get $get): bool => $this->hasCategory($get('game_id'), $get('gender'), $get('age_group')))
                    ->schema(collect(Position::cases())->map(fn (Position $position): Fieldset => $this->rankFieldset($position))->all()),
            ]);
    }

    protected function rankFieldset(Position $position): Fieldset
    {
        $key = $position->value;

        $medal = match ($position) {
            Position::First => '🥇 1st place',
            Position::Second => '🥈 2nd place',
            Position::Third => '🥉 3rd place',
        };

        return Fieldset::make("{$medal} ({$position->formal()} क्रमांक)")
            ->schema([
                Select::make("{$key}_member_id")
                    ->label('Registered member')
                    ->placeholder('Type a name or mobile to search')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => $this->searchMembers($search))
                    ->getOptionLabelUsing(fn ($value): ?string => $this->memberLabel(Member::query()->with('family')->find($value))),
                TextInput::make("{$key}_name")
                    ->label('Or name (not a member)')
                    ->placeholder('Printed on the certificate')
                    ->maxLength(255),
            ])
            ->columns(['default' => 1, 'sm' => 2]);
    }

    /**
     * @return array<string, string>
     */
    protected function searchMembers(string $search): array
    {
        return Member::query()
            ->with('family')
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%"))
            ->orderBy('name')
            ->limit(30)
            ->get()
            ->mapWithKeys(fn (Member $member): array => [$member->id => $this->memberLabel($member)])
            ->all();
    }

    protected function memberLabel(?Member $member): ?string
    {
        if (! $member) {
            return null;
        }

        return $member->family?->plot_number
            ? "{$member->name} (Plot {$member->family->plot_number})"
            : $member->name;
    }

    protected function hasCategory(mixed $gameId, mixed $gender, mixed $group): bool
    {
        return filled($gameId) && filled($gender) && filled($group);
    }

    protected function categoryTitle(mixed $gender, mixed $group): string
    {
        $gender = Gender::tryFrom((string) $gender);
        $group = AgeGroup::tryFrom((string) $group);

        if (! $gender || ! $group) {
            return '';
        }

        return $group->marathi().' — '.$group->genderLabel($gender);
    }

    /**
     * @return array<string, null>
     */
    protected function blankWinners(): array
    {
        $blank = [];

        foreach (Position::cases() as $position) {
            $blank["{$position->value}_member_id"] = null;
            $blank["{$position->value}_name"] = null;
        }

        return $blank;
    }

    /**
     * Reload the form for the selected game/gender/group, filling in whatever is already saved.
     */
    protected function loadCategory(bool $resetCategory = false): void
    {
        $state = $this->form->getRawState();

        $gameId = $state['game_id'] ?? null;
        $gender = $resetCategory ? null : ($state['gender'] ?? null);
        $group = $resetCategory ? null : ($state['age_group'] ?? null);

        $winners = $this->blankWinners();

        if ($this->hasCategory($gameId, $gender, $group)) {
            GameParticipant::query()
                ->where('game_id', $gameId)
                ->where('gender', $gender)
                ->where('age_group', $group)
                ->get()
                ->each(function (GameParticipant $participant) use (&$winners): void {
                    $winners["{$participant->position->value}_member_id"] = $participant->member_id;
                    $winners["{$participant->position->value}_name"] = $participant->participant_name;
                });
        }

        $this->form->fill([
            'game_id' => $gameId,
            'gender' => $gender,
            'age_group' => $group,
            ...$winners,
        ]);
    }

    public function pick(string $gender, string $group): void
    {
        $this->form->fill([
            'game_id' => $this->data['game_id'] ?? null,
            'gender' => Gender::from($gender)->value,
            'age_group' => AgeGroup::from($group)->value,
            ...$this->blankWinners(),
        ]);

        $this->loadCategory();
    }

    public function save(): void
    {
        abort_unless(static::canAccess(), 403);

        $data = $this->form->getState();

        $game = Game::query()->findOrFail($data['game_id']);
        $gender = Gender::from($data['gender']);
        $group = AgeGroup::from($data['age_group']);

        $entries = [];

        foreach (Position::cases() as $position) {
            $memberId = $data["{$position->value}_member_id"] ?? null;
            $name = trim((string) ($data["{$position->value}_name"] ?? ''));

            $entries[$position->value] = [
                'member_id' => $memberId ?: null,
                'participant_name' => $memberId || $name === '' ? null : $name,
            ];
        }

        $duplicates = collect($entries)
            ->map(fn (array $entry): ?string => $entry['member_id']
                ? 'member-'.$entry['member_id']
                : ($entry['participant_name'] ? 'name-'.mb_strtolower($entry['participant_name']) : null))
            ->filter()
            ->duplicates();

        if ($duplicates->isNotEmpty()) {
            Notification::make()
                ->title('The same person is listed for more than one rank')
                ->body('Nothing was saved. Please fix it and try again.')
                ->danger()
                ->send();

            return;
        }

        DB::transaction(function () use ($game, $gender, $group, $entries): void {
            foreach (Position::cases() as $position) {
                $entry = $entries[$position->value];
                $rank = [
                    'game_id' => $game->id,
                    'age_group' => $group,
                    'gender' => $gender,
                    'position' => $position,
                ];

                if ($entry['member_id'] || $entry['participant_name']) {
                    GameParticipant::updateOrCreate($rank, $entry);
                } else {
                    GameParticipant::query()->where($rank)->delete();
                }
            }
        });

        $this->loadCategory();

        Notification::make()
            ->title('Winners saved')
            ->body("{$game->name} — {$this->categoryTitle($gender->value, $group->value)}")
            ->success()
            ->send();
    }

    /**
     * @return array<int, Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save winners')
                ->submit('save')
                ->visible(fn (): bool => $this->hasCategory(
                    $this->data['game_id'] ?? null,
                    $this->data['gender'] ?? null,
                    $this->data['age_group'] ?? null,
                )),
        ];
    }

    /**
     * Winners entered so far for the selected game, per gender and group.
     *
     * @return list<array{gender: Gender, cells: list<array{group: AgeGroup, gender: Gender, count: int, active: bool}>}>
     */
    public function getProgress(): array
    {
        $gameId = $this->data['game_id'] ?? null;

        if (blank($gameId)) {
            return [];
        }

        $counts = GameParticipant::query()
            ->where('game_id', $gameId)
            ->selectRaw('gender, age_group, count(*) as total')
            ->groupBy('gender', 'age_group')
            ->get()
            ->mapWithKeys(fn (GameParticipant $row): array => [
                $row->gender->value.'.'.$row->age_group->value => (int) $row->total,
            ]);

        return collect(Gender::cases())
            ->map(fn (Gender $gender): array => [
                'gender' => $gender,
                'cells' => collect(AgeGroup::cases())
                    ->map(fn (AgeGroup $group): array => [
                        'group' => $group,
                        'gender' => $gender,
                        'count' => $counts->get($gender->value.'.'.$group->value, 0),
                        'active' => ($this->data['gender'] ?? null) === $gender->value
                            && ($this->data['age_group'] ?? null) === $group->value,
                    ])
                    ->all(),
            ])
            ->all();
    }
}
