<?php

use App\Models\Project;
use App\Models\Activity;
use App\Models\User;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public Activity $activity;

    #[Rule('required')]
    public ?int $project_id = null;

    #[Rule('required|date')]
    public string $date = '';

    #[Rule('required')]
    public string $title = '';

    #[Rule('nullable')]
    public ?string $description = null;

    public Illuminate\Support\Collection $projectsSearchable;

    public function mount(): void
    {
        if ($this->activity->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $this->fill($this->activity);
        $this->search();
    }

    public function search(string $value = ''): void
    {
        $selectedOption = Project::where('id', $this->project_id)->get();

        $this->projectsSearchable = Project::query()
            ->where('is_active', true)
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get()
            ->merge($selectedOption);
    }

    public function save(): void
    {
        $data = $this->validate();
        $this->activity->update($data);
        $this->success('Activity updated with success.', redirectTo: '/activity');
    }

    public function with(): array
    {
        return [];
    }
}; ?>

<div>
    <x-header title="Update Activity" separator />

    <x-form wire:submit="save">
        <div class="grid gap-5 lg:grid-cols-2">
            <div class="space-y-4">
                <x-choices label="Project" wire:model="project_id" :options="$projectsSearchable" placeholder="Select Project" icon="o-cube" inline single searchable />
                <x-datetime label="Date" wire:model="date" icon="o-calendar" inline />
            </div>
            <div class="space-y-4">
                <x-input label="Title/Activity" wire:model="title" inline />
            </div>
        </div>
        <x-markdown label="Note/Description" wire:model="description" class="mt-4" />

        <x-slot:actions>
            <x-button label="Cancel" link="/activity" />
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
