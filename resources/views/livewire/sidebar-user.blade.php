<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    #[On('profile-updated')]
    public function refresh(): void
    {
        // Simply triggers a re-render
    }

    public function with(): array
    {
        return [
            'user' => auth()->user(),
        ];
    }
}; ?>

<div>
    @if($user)
        <x-menu-separator />

        <x-list-item :item="$user" value="name" no-separator no-hover class="-mx-2 !-my-2 rounded transition-all duration-500">
            <x-slot:avatar>
                <x-avatar image="{{ $user->avatar ?? '/empty-user.png' }}" class="!w-10 shadow-sm border border-base-200" />
            </x-slot:avatar>
            <x-slot:actions>
                <div x-data="{ logoutModal: false }">
                    <x-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff" @click="logoutModal = true" />

                    <template x-teleport="body">
                        <dialog :class="{ 'modal-open': logoutModal }" class="modal">
                            <div class="modal-box">
                                <h3 class="text-lg font-bold">Konfirmasi Logoff</h3>
                                <div class="py-4">Apakah Anda yakin ingin keluar dari aplikasi?</div>
                                <div class="modal-action">
                                    <x-button label="Batal" @click="logoutModal = false" />
                                    <x-button label="Ya, Keluar" link="/logout" no-wire-navigate class="btn-error" />
                                </div>
                            </div>
                            <div class="modal-backdrop bg-black/20" @click="logoutModal = false">
                                <button>close</button>
                            </div>
                        </dialog>
                    </template>
                </div>
            </x-slot:actions>
        </x-list-item>

        <x-menu-separator />
    @endif
</div>
