<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Password;

new
#[Layout('layouts.empty')]
#[Title('Forgot Password')]
class extends Component {

	#[Rule('required|email')]
	public string $email = '';

	public function mount()
	{
		if (auth()->user()) {
			return redirect()->route('dashboard');
		}
	}

	public function send()
	{
		$this->validate();

		$status = Password::sendResetLink(['email' => $this->email]);

		if ($status == Password::RESET_LINK_SENT) {
			session()->flash('status', __($status));
			return redirect()->route('login');
		}

		$this->addError('email', __($status));
	}
}
?>

@section('title', 'Forgot Password')

<div>
	<div class="md:w-96 mx-auto mt-20">
		<div class="mb-10">
			<x-app-brand />
		</div>

		@if (session('status'))
			<x-alert title="{{ session('status') }}" icon="o-check-circle" class="alert-success mb-6" />
		@endif

		<x-form wire:submit="send">
			<x-input placeholder="Email" wire:model="email" icon="o-envelope" inline />

			<x-slot:actions>
				<div class="flex flex-col w-full gap-2">
					<x-button label="Send Reset Link" type="submit" class="btn-primary w-full" spinner="send" />
					<x-button label="Back to Login" link="/login" class="btn-ghost w-full" />
				</div>
			</x-slot:actions>
		</x-form>
	</div>
</div>
