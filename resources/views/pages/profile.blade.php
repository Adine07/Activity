<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Rule as LivewireRule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithFileUploads;

    public User $user;

    public string $name = '';

    public string $email = '';

    public $avatar;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount()
    {
        $this->user = auth()->user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    public function save()
    {
        $data = $this->validate([
            'name' => 'required|min:3',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
            'avatar' => 'nullable|image|max:1024',
            'password' => 'nullable|confirmed|min:8',
        ]);

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $this->user->avatar = '/storage/'.$path;
        }

        if ($this->password) {
            $this->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);

            $this->user->password = Hash::make($this->password);
        }

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->save();

        $this->dispatch('profile-updated');

        $this->success('Profile updated successfully!', position: 'toast-bottom');

        $this->reset(['current_password', 'password', 'password_confirmation', 'avatar']);
    }
}; ?>

@section('title', 'Profile')

<div>
    <x-header title="Account Settings" subtitle="Manage your personal information and security" separator progress-indicator />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <div class="lg:col-span-1">
            <x-card title="Profile Photo" subtitle="Update your avatar" shadow separator>
                <div class="flex flex-col items-center justify-center py-6">
                    <x-file wire:model="avatar" accept="image/*" crop-after-change>
                        <img src="{{ $avatar ? $avatar->temporaryUrl() : ($user->avatar ?? '/empty-user.jpg') }}" class="w-48 h-48 rounded-3xl object-cover shadow-2xl border-4 border-base-200" />
                    </x-file>
                    <p class="text-[10px] uppercase font-black tracking-widest text-base-content/20 mt-4">Click image to upload</p>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-form wire:submit="save">
                <x-card title="Basic Information" subtitle="Your public identify" shadow separator>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Full Name" wire:model="name" icon="o-user" placeholder="Your name" />
                        <x-input label="Email Address" wire:model="email" icon="o-envelope" placeholder="email@example.com" />
                    </div>
                </x-card>

                <x-card title="Security" subtitle="Keep your account safe" shadow separator class="mt-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Current Password" wire:model="current_password" type="password" icon="o-lock-closed" placeholder="Required to change password" class="md:col-span-2" />
                        <x-input label="New Password" wire:model="password" type="password" icon="o-key" placeholder="Leave blank to keep current" />
                        <x-input label="Confirm Password" wire:model="password_confirmation" type="password" icon="o-key" placeholder="Confirm your new password" />
                    </div>
                    <div class="mt-4 flex items-center gap-2 bg-warning/10 text-warning p-4 rounded-xl">
                        <x-icon name="o-exclamation-triangle" class="w-5 h-5 shrink-0" />
                        <p class="text-xs font-bold uppercase tracking-widest">Only fill if you want to change your password</p>
                    </div>
                </x-card>

                <div class="mt-8 flex justify-end gap-3 bg-base-100 p-4 rounded-2xl border border-base-200 shadow-sm">
                    <x-button label="Discard" @click="window.location.reload()" class="btn-ghost" />
                    <x-button label="Save Changes" icon="o-check" class="btn-primary" type="submit" spinner="save" />
                </div>
            </x-form>
        </div>
    </div>
</div>
