<div {{ $attributes }}>
    @php
        $menus = [
            ['title' => 'Dashboard', 'icon' => 'o-squares-2x2', 'link' => route('dashboard')],
            ['title' => 'Timesheet', 'icon' => 'o-clipboard-document-list', 'link' => route('timesheet.index')],
            ['title' => 'Projects', 'icon' => 'o-code-bracket-square', 'link' => route('projects.index')],
            ['title' => 'Users', 'icon' => 'o-users', 'link' => route('users.index')],
            ['title' => 'Profile', 'icon' => 'o-user-circle', 'link' => route('profile')],
        ];
    @endphp

    <x-app-brand class="px-5 pt-4" />

    <x-menu activate-by-route>

        <livewire:sidebar-user />

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
