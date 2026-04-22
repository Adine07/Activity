<?php

use App\Models\User;
use App\Models\Country;
use Mary\Traits\Toast;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use Toast;


    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public bool $deleteModal = false;
    public bool $exportModal = false;
    public ?User $selectedUser = null;
    public ?User $selectedUserForExport = null;
    public ?string $selectedMonth = null;
    public ?string $selectedYear = null;
    public string $exportFormat = 'excel';

    public function mount()
    {
        $this->selectedMonth = now()->format('m');
        $this->selectedYear = now()->format('Y');
    }

    public function months(): array
    {
        return [
            ['id' => '01', 'name' => 'Januari'],
            ['id' => '02', 'name' => 'Februari'],
            ['id' => '03', 'name' => 'Maret'],
            ['id' => '04', 'name' => 'April'],
            ['id' => '05', 'name' => 'Mei'],
            ['id' => '06', 'name' => 'Juni'],
            ['id' => '07', 'name' => 'Juli'],
            ['id' => '08', 'name' => 'Agustus'],
            ['id' => '09', 'name' => 'September'],
            ['id' => '10', 'name' => 'Oktober'],
            ['id' => '11', 'name' => 'November'],
            ['id' => '12', 'name' => 'Desember'],
        ];
    }

    public function years(): array
    {
        $currentYear = (int) now()->format('Y');
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[] = ['id' => (string) $i, 'name' => (string) $i];
        }
        return $years;
    }

    public function formats(): array
    {
        return [
            ['id' => 'excel', 'name' => 'Excel'],
            ['id' => 'pdf', 'name' => 'PDF'],
        ];
    }

    public function filterCount(): int
    {
        // Daftar property yang dianggap sebagai filter
        $filters = [
            $this->search,
        ];

        return collect($filters)
            ->filter(fn($value) => !empty($value) && $value != 0 && $value != "0")
            ->count();
    }

    // Reset pagination when any component property changes
    public function updated($property): void
    {
        if (! is_array($property) && $property != "") {
            $this->resetPage();
        }
    }

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function confirmDelete(User $user): void
    {
        $this->selectedUser = $user;
        $this->deleteModal = true;
    }

    public function delete(): void
    {
        if ($this->selectedUser) {
            $name = $this->selectedUser->name;
            $this->selectedUser->delete();
            $this->warning("$name deleted", 'Good bye!', position: 'toast-bottom');
            $this->deleteModal = false;
            $this->selectedUser = null;
        }
    }

    public function showExportModal(User $user): void
    {
        $this->selectedUserForExport = $user;
        $this->exportModal = true;
    }

    public function export()
    {
        if (!$this->selectedUserForExport) {
            return;
        }

        \Carbon\Carbon::setLocale('id');

        $userId = $this->selectedUserForExport->id;
        $month = $this->selectedMonth;
        $year = $this->selectedYear;
        $format = $this->exportFormat;

        $fileName = "activity_{$this->selectedUserForExport->name}_{$year}_{$month}." . ($format == 'excel' ? 'xlsx' : 'pdf');

        $this->exportModal = false;
        $this->success("Exporting data...");

        if ($format == 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UserActivityExport($userId, $month, $year), $fileName);
        } else {
            $activities = \App\Models\Activity::with('project')
                ->where('user_id', $userId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date')
                ->get();

            $user = $this->selectedUserForExport;
            $monthName = collect($this->months())->firstWhere('id', $month)['name'];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.user-activity-pdf', compact('activities', 'user', 'monthName', 'year'))
                ->setPaper('a4', 'landscape');

            return response()->streamDownload(fn () => print($pdf->output()), $fileName);
        }
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'avatar', 'label' => '', 'class' => 'w-1'],
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'],
            ['key' => 'email', 'label' => 'Email', 'class' => 'w-64'],
            ['key' => 'role_name', 'label' => 'Role', 'sortable' => false],
        ];
    }

    /**
     * For demo purpose, this is a static collection.
     *
     * On real projects you do it with Eloquent collections.
     * Please, refer to maryUI docs to see the eloquent examples.
     */
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->withAggregate('role', 'name')
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(5);
    }

    public function with(): array
    {
        return [
            'users' => $this->users(),
            'headers' => $this->headers(),
            'filterCount' => $this->filterCount(),
            'months' => $this->months(),
            'years' => $this->years(),
            'formats' => $this->formats(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Users" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" :badge="$filterCount > 0 ? $filterCount : null" />
            <x-button label="Create" link="/users/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination link="users/{id}/edit">
            @scope('cell_avatar', $user)
                <x-avatar image="{{ $user->avatar ?? '/empty-user.jpg' }}" class="!w-10 !h-10" />
            @endscope
            @scope('actions', $user)
            <div class="flex gap-2">
                <div class="tooltip tooltip-left" data-tip="Export Activity">
                    <x-button icon="o-document-arrow-down" wire:click="showExportModal({{ $user['id'] }})" spinner class="btn-ghost btn-sm text-primary" />
                </div>
                <div class="tooltip tooltip-left" data-tip="Hapus Data">
                    <x-button icon="o-trash" wire:click="confirmDelete({{ $user['id'] }})" spinner class="btn-ghost btn-sm text-error" />
                </div>
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
        @if($selectedUser)
            <div>Apakah Anda yakin ingin menghapus user <strong>{{ $selectedUser->name }}</strong>?</div>
        @endif
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.deleteModal = false" />
            <x-button label="Ya, Hapus" wire:click="delete" spinner class="btn-error" />
        </x-slot:actions>
    </x-modal>

    <!-- EXPORT MODAL -->
    <x-modal wire:model="exportModal" title="Export Activity" subtitle="Pilih periode dan format export" separator>
        @if($selectedUserForExport)
            <div class="mb-4 text-lg">User: <span class="font-bold text-primary">{{ $selectedUserForExport->name }}</span></div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-select label="Bulan" :options="$months" wire:model="selectedMonth" icon="o-calendar" />
            <x-select label="Tahun" :options="$years" wire:model="selectedYear" icon="o-calendar-days" />
            <x-select label="Format" :options="$formats" wire:model="exportFormat" icon="o-document" />
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.exportModal = false" />
            <x-button label="Export" wire:click="export" spinner class="btn-primary" icon="o-arrow-down-tray" />
        </x-slot:actions>
    </x-modal>
</div>
