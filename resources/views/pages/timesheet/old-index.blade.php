<?php

use App\Models\Timesheet;
use Mary\Traits\Toast;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use Toast;

    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'date', 'direction' => 'desc'];

    public bool $deleteModal = false;

    public ?Timesheet $selectedTimesheet = null;

    public function filterCount(): int
    {
        $filters = [$this->search];

        return collect($filters)->filter(fn ($value) => ! empty($value))->count();
    }

    public function updated($property): void
    {
        if (! is_array($property) && $property != '') {
            $this->resetPage();
        }
    }

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function confirmDelete(Timesheet $timesheet): void
    {
        $this->selectedTimesheet = $timesheet;
        $this->deleteModal = true;
    }

    public function delete(): void
    {
        if ($this->selectedTimesheet) {
            $title = $this->selectedTimesheet->title;
            $this->selectedTimesheet->delete();
            $this->warning("$title deleted", position: 'toast-bottom');
            $this->deleteModal = false;
            $this->selectedTimesheet = null;
        }
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'date', 'label' => 'Date', 'class' => 'w-32'],
            ['key' => 'user_name', 'label' => 'User', 'class' => 'w-48'],
            ['key' => 'project_name', 'label' => 'Project', 'class' => 'w-48'],
            ['key' => 'title', 'label' => 'Activity'],
        ];
    }

    public function timesheets(): LengthAwarePaginator
    {
        return Timesheet::query()
            ->withAggregate('user', 'name')
            ->withAggregate('project', 'name')
            ->when($this->search, function (Builder $q) {
                $q->where('title', 'like', "%$this->search%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%$this->search%"))
                    ->orWhereHas('project', fn ($q) => $q->where('name', 'like', "%$this->search%"));
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate(10);
    }

    public function with(): array
    {
        return [
            'timesheets' => $this->timesheets(),
            'headers' => $this->headers(),
            'filterCount' => $this->filterCount(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Timesheet" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search activity, user, or project..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" :badge="$filterCount > 0 ? $filterCount : null" />
            <x-button label="Create" link="/timesheet/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$timesheets" :sort-by="$sortBy" with-pagination link="timesheet/{id}/edit">
            @scope('cell_date', $timesheet)
                 {{ \Carbon\Carbon::parse($timesheet->date)->format('d M Y') }}
            @endscope
            @scope('actions', $timesheet)
                <div class="tooltip tooltip-left" data-tip="Hapus Data">
                    <x-button icon="o-trash" wire:click="confirmDelete({{ $timesheet->id }})" spinner class="btn-ghost btn-sm text-error" />
                </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>

    <!-- DELETE MODAL -->
    <x-modal wire:model="deleteModal" title="Konfirmasi Hapus" subtitle="Tindakan ini tidak dapat dibatalkan" separator>
        @if($selectedTimesheet)
            <div>Apakah Anda yakin ingin menghapus timesheet <strong>{{ $selectedTimesheet->title }}</strong>?</div>
        @endif
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.deleteModal = false" />
            <x-button label="Ya, Hapus" wire:click="delete" spinner class="btn-error" />
        </x-slot:actions>
    </x-modal>
</div>
