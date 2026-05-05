<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

new
#[Layout('layouts.empty')]
#[Title('Reset Password')]
class extends Component {

	public string $token = '';

	#[Rule('required|email')]
	public string $email = '';

	#[Rule('required|confirmed|min:8')]
	public string $password = '';

	public string $password_confirmation = '';

	public function mount($token = null)
	{
		if (auth()->user()) {
			return redirect()->route('dashboard');
		}

		$this->token = $token ?? '';
		$this->email = request()->query('email', '');
	}

	public function submitReset()
	{
		$this->validate([
			'email' => 'required|email',
			'password' => 'required|confirmed|min:8',
		]);

		$status = Password::reset(
			['email' => $this->email, 'password' => $this->password, 'password_confirmation' => $this->password_confirmation, 'token' => $this->token],
			function ($user, $password) {
				$user->password = Hash::make($password);
				$user->save();
			}
		);

		if ($status == Password::PASSWORD_RESET) {
			return redirect()->route('login')->with('status', __($status));
		}

		$this->addError('email', __($status));
	}
};
?>

@section('title', 'Reset Password')

<div>
	<div class="md:w-96 mx-auto mt-20">
		<div class="mb-10">
			<x-app-brand />
		</div>

		@if (session('status'))
			<x-alert title="{{ session('status') }}" icon="o-check-circle" class="alert-success mb-6" />
		@endif

		<x-form wire:submit="submitReset">
			<input type="hidden" wire:model="token" />
			<x-input placeholder="Email" wire:model="email" icon="o-envelope" inline />
			<x-input placeholder="New Password" wire:model="password" type="password" icon="o-key" inline />
			<x-input placeholder="Confirm Password" wire:model="password_confirmation" type="password" icon="o-key" inline />

			<x-slot:actions>
				<div class="flex flex-col w-full gap-2">
					<x-button label="Reset Password" type="submit" class="btn-primary w-full" spinner="submitReset" />
					<x-button label="Back to Login" link="/login" class="btn-ghost w-full" />
				</div>
			</x-slot:actions>
		</x-form>
	</div>
</div>
