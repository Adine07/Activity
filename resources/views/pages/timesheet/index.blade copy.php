<?php

use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public ?int $selectedUserId = null;

    public string $selectedDate = '';

    public function mount()
    {
        $this->selectedUserId = auth()->id();
        $this->selectedDate = date('Y-m-d');
    }

    public function prevDate()
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->toDateString();
    }

    public function nextDate()
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->toDateString();
    }

    public function selectToday()
    {
        $this->selectedDate = date('Y-m-d');
    }

    public function with(): array
    {
        $users = User::all();

        $timesheets = Timesheet::where('user_id', $this->selectedUserId)
            ->where('date', $this->selectedDate)
            ->with(['project'])
            ->get();

        return [
            'users' => $users,
            'timesheets' => $timesheets,
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Timesheet Board" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Add Activity" link="/timesheet/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- USER TABS -->
    <div class="mb-8 overflow-x-auto pb-2 scrollbar-hide">
        <div class="flex gap-2 flex-nowrap">
            @foreach($users as $user)
                <button
                    wire:click="$set('selectedUserId', {{ $user->id }})"
                    class="btn btn-sm lg:btn-md {{ $selectedUserId == $user->id ? 'btn-primary shadow-lg shadow-primary/20' : 'btn-ghost bg-base-100 border-base-200' }} flex items-center gap-3 px-4 transition-all"
                >
                    <x-avatar :image="$user->avatar ?? '/empty-user.jpg'" class="!w-6 !h-6" />
                    <span class="font-bold">{{ $user->name }}</span>
                    @if($selectedUserId == $user->id)
                        <div class="w-1.5 h-1.5 rounded-full bg-primary-content animate-pulse"></div>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    <!-- DATE NAVIGATION -->
    <div class="max-w-xl mx-auto mb-10">
        <div class="flex items-center justify-between bg-base-100 p-2 lg:p-4 rounded-2xl shadow-sm border border-base-200">
            <x-button icon="o-chevron-left" wire:click="prevDate" class="btn-circle btn-ghost" />

            <div class="text-center flex flex-col items-center">
                <button wire:click="selectToday" class="group cursor-pointer">
                    <h2 class="text-xl lg:text-3xl font-black uppercase tracking-tighter lg:tracking-[0.1em] transition-all group-hover:text-primary">
                        @if($selectedDate == date('Y-m-d'))
                            TODAY
                        @elseif($selectedDate == date('Y-m-d', strtotime('-1 day')))
                            YESTERDAY
                        @else
                            {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                        @endif
                    </h2>
                    @if($selectedDate != date('Y-m-d'))
                        <span class="text-[9px] font-black text-primary uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all">Go to Today</span>
                    @else
                        <span class="text-[9px] font-black text-base-content/20 uppercase tracking-widest">{{ \Carbon\Carbon::parse($selectedDate)->format('l') }}</span>
                    @endif
                </button>
            </div>

            <x-button icon="o-chevron-right" wire:click="nextDate" class="btn-circle btn-ghost" />
        </div>
    </div>

    <!-- ACTIVITY BOARD -->
    <div class="max-w-4xl mx-auto space-y-6">
        @forelse($timesheets as $item)
            <div class="group relative">
                {{-- Connector Line --}}
                @if(!$loop->last)
                    <div class="absolute left-6 top-12 bottom-0 w-0.5 bg-base-300 -mb-6 hidden lg:block"></div>
                @endif

                <x-card class="bg-base-100 shadow-none border border-base-200 hover:border-primary/50 hover:shadow-xl hover:shadow-primary/5 transition-all overflow-hidden" link="/timesheet/{{ $item->id }}/edit">
                    <div class="flex items-start gap-4 lg:gap-8">
                        <div class="w-10 h-10 lg:w-14 lg:h-14 rounded-2xl bg-base-200 flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:rotate-6 transition-all duration-300">
                            <x-icon name="o-bolt" class="w-5 h-5 lg:w-8 lg:h-8 text-base-content/20 group-hover:text-primary-content transition-colors" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2">
                                <h3 class="text-lg lg:text-xl font-black tracking-tight truncate group-hover:text-primary transition-colors">{{ $item->title }}</h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase text-base-content/30 tracking-widest">{{ $item->created_at->format('H:i') }}</span>
                                    <x-badge value="{{ $item->project->name ?? 'Internal' }}" class="badge-neutral font-black uppercase text-[9px] tracking-widest px-3" />
                                </div>
                            </div>

                            @if($item->description)
                                <div class="mt-4 text-sm text-base-content/70 line-clamp-3 prose prose-sm max-w-none border-l-2 border-base-200 pl-4 py-1 italic">
                                    {!! Str::markdown($item->description) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </x-card>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-32 opacity-10 grayscale sepia">
                <x-icon name="o-calendar" class="w-24 h-24 mb-6" />
                <p class="text-2xl font-black uppercase tracking-[0.2em]">Silent Day</p>
                <p class="text-xs mt-2 font-bold uppercase tracking-widest">No activities recorded for this date</p>
            </div>
        @endforelse
    </div>
</div>
