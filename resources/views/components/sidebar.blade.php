<div {{ $attributes }}>
    @php
        $menus = [
            ['title' => 'Dashboard', 'icon' => 'o-squares-2x2', 'link' => route('dashboard')],
            ['title' => 'Timesheet', 'icon' => 'o-squares-2x2', 'link' => route('timesheet.index')],
            ['title' => 'Projects', 'icon' => 'o-squares-2x2', 'link' => route('projects.index')],
            ['title' => 'Users', 'icon' => 'o-squares-2x2', 'link' => route('users.index')],
        ];
    @endphp

    {{-- BRAND --}}
    <x-app-brand class="px-5 pt-4" />

    {{-- MENU --}}
    <x-menu activate-by-route>

        {{-- User --}}
        @if($user = auth()->user())
            <x-menu-separator />

            <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                <x-slot:avatar>
                    <x-avatar image="{{ $user->avatar ?? '/empty-user.jpg' }}" class="!w-10" />
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

        @foreach($menus as $m)
            @if(isset($m['children']))
                <x-menu-sub title="{{ $m['title'] }}" icon="{{ $m['icon'] }}">
                    @foreach($m['children'] as $c)
                        <x-menu-item title="{{ $c['title'] }}" icon="{{ $c['icon'] }}" link="{{ $c['link'] }}" />
                    @endforeach
                </x-menu-sub>
            @else
                <x-menu-item title="{{ $m['title'] }}" icon="{{ $m['icon'] }}" link="{{ $m['link'] }}" />
            @endif
        @endforeach

    </x-menu>
</div>
