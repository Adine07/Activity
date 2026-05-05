<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

new class extends Component
{
    // We will use it later
    use Toast, WithFileUploads;

    // You could use Livewire "form object" instead.
    #[Rule('required')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required')]
    public string $password = '';

    #[Rule('required')]
    public string $role_id = '';

    #[Rule('nullable|image|max:1024')]
    public $photo;

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create
        $data['password'] = bcrypt('password'); // Just for demo, never do this in production
        $user = User::create($data);

        // Upload file and save the avatar `url` on User model
        if ($this->photo) {
            $url = $this->photo->store('users', 'public');
            $user->update(['avatar' => "/storage/$url"]);
        }

        // You can toast and redirect to any route
        $this->success('User created with success.', redirectTo: '/users');
    }

    // We also need this to fill Countries combobox on upcoming form
    public function with(): array
    {
        return [
            'roles' => Role::all(),
        ];
    }
};
?>

<div>
        @section('title', 'Create User')
    <x-header title="Create User" separator />

    <div class="grid gap-5 lg:grid-cols-2">
        <div>
            <x-form wire:submit="save">
                <x-file label="Avatar" wire:model="photo" accept="image/png, image/jpeg" crop-after-change>
                    <img src="/empty-user.jpg" class="h-36 rounded-lg" />
                </x-file>

                <x-input label="Name" wire:model="name" />
                <x-input label="Email" wire:model="email" />
                <x-input label="Password" wire:model="password" />
                <x-select label="Role" wire:model="role_id" :options="$roles" placeholder="Select User Role" />

                <x-slot:actions>
                    <x-button label="Cancel" link="/users" />
                    {{-- The important thing here is `type="submit"` --}}
                    {{-- The spinner property is nice! --}}
                    <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
                </x-slot:actions>
            </x-form>
        </div>
        <div>
            {{-- Get a nice picture from `StorySet` web site --}}
            <img src="https://onlinepngtools.com/images/png/illustrations/preview-png-on-colorful-background.png" width="400" class="mx-auto mt-32" />
        </div>
    </div>
</div>
