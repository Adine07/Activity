<?php

use Livewire\Component;
use App\Models\Project;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;

new class extends Component {
    use Toast;

    public Project $project;

    #[Rule('required')]
    public string $name = '';

    #[Rule('nullable')]
    public ?string $description = null;

    #[Rule('nullable')]
    public ?string $git_repo = null;

    #[Rule('nullable')]
    public ?string $live_url = null;

    public bool $is_active = true;

    public function mount(): void
    {
        if (auth()->user()->role_id != 1 && $this->project->created_by != auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $this->fill($this->project);
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['is_active'] = $this->is_active;

        $this->project->update($data);

        $this->success('Project updated with success.', redirectTo: '/projects');
    }
}; ?>

@section('title', 'Edit Project')

<div>
    <x-header title="Update {{ $project->name }}" separator />

    <x-form wire:submit="save">
        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <x-input label="Name" wire:model="name" />
                <x-markdown label="Description" wire:model="description" class="mt-4" />
            </div>
            <div class="space-y-4">
                <x-input label="Git Repository" wire:model="git_repo" icon="o-link" />
                <x-input label="Live URL" wire:model="live_url" icon="o-globe-alt" />
                <x-toggle label="Is Active" wire:model="is_active" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/projects" />
            <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
