<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('layouts.empty')]
#[Title('Login')]
class extends Component {

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required')]
    public string $password = '';

    public function mount()
    {
        // It is logged in
        if (auth()->user()) {
            return redirect('/');
        }
    }

    public function login()
    {
        $credentials = $this->validate();

        if (auth()->attempt($credentials)) {
            request()->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }
}
?>

@section('title', 'Login')

<div>
    <div class="md:w-96 mx-auto mt-20">
        <div class="mb-10">
            <x-app-brand />
        </div>

        @if (session('status'))
            <x-alert title="{{ session('status') }}" icon="o-check-circle" class="alert-success mb-6" />
        @endif

        <x-form wire:submit="login">
            <x-input placeholder="Email" wire:model="email" icon="o-user" inline />
            <x-input placeholder="Password" wire:model="password" type="password" icon="o-key" inline />

            <x-slot:actions>
                <div class="flex flex-col w-full gap-2">
                    <x-button label="Login" type="submit" icon="o-paper-airplane" class="btn-primary w-full" spinner="login" />
                    <x-button label="Forgot Password?" link="/forgot-password" class="btn-ghost w-full" />
                </div>
            </x-slot:actions>
        </x-form>
    </div>
</div>
