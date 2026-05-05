<?php

use Livewire\Component;
use App\Models\Project;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;

new class extends Component {
    use Toast;

    #[Rule('required')]
    public string $name = '';

    #[Rule('nullable')]
    public ?string $description = null;

    #[Rule('nullable')]
    public ?string $git_repo = null;

    #[Rule('nullable')]
    public ?string $live_url = null;

    public bool $is_active = true;

    public function save(): void
    {
        $data = $this->validate();
        $data['is_active'] = $this->is_active;
        $data['created_by'] = auth()->id();

        Project::create($data);

        $this->success('Project created with success.', redirectTo: '/projects');
    }
}; ?>

@section('title', 'Create Project')

<div>
    <x-header title="Create Project" separator />

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
