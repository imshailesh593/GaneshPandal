<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions :actions="$this->getFormActions()" />
    </x-filament-panels::form>

    @if ($progress = $this->getProgress())
        <x-filament::section
            heading="Progress for this game"
            description="Tap a group to open it. Each group is saved separately."
        >
            @foreach ($progress as $row)
                <p style="font-weight: 600; margin: {{ $loop->first ? '0' : '1rem' }} 0 0.5rem;">
                    {{ $row['gender']->getLabel() }}
                </p>

                <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.5rem;">
                    @foreach ($row['cells'] as $cell)
                        <x-filament::button
                            wire:click="pick('{{ $cell['gender']->value }}', '{{ $cell['group']->value }}')"
                            size="sm"
                            :outlined="! $cell['active']"
                            :color="$cell['count'] === 3 ? 'success' : ($cell['count'] > 0 ? 'warning' : 'gray')"
                        >
                            {{ $cell['group']->marathi() }} · {{ $cell['count'] }}/3
                        </x-filament::button>
                    @endforeach
                </div>
            @endforeach
        </x-filament::section>
    @endif
</x-filament-panels::page>
