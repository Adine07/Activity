<?php

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    #[Livewire\Attributes\Url(as: 'user')]
    public ?int $selectedUserId = null;

    #[Livewire\Attributes\Url(as: 'date')]
    public string $selectedDate = '';

    public ?string $selectedActivityId = null;

    public function mount()
    {
        $this->selectedUserId ??= auth()->id();
        $this->selectedDate = $this->selectedDate ?: date('Y-m-d');
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedDate', 'selectedUserId'])) {
            $this->selectedActivityId = null;
        }
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

    public function delete(Activity $activity): void
    {
        if ($activity->user_id != auth()->id()) {
            $this->error('Unauthorized action.', position: 'toast-bottom');

            return;
        }

        $activity->delete();
        $this->selectedActivityId = null;
        $this->success('Activity entry deleted.', position: 'toast-bottom');
    }

    public function with(): array
    {
        if (auth()->user()->role_id != 1) {
            $this->selectedUserId = auth()->id();
        }

        $users = User::all();

        $activities = Activity::where('user_id', $this->selectedUserId)
            ->where('date', $this->selectedDate)
            ->with(['project'])
            ->get();

        if ($activities->count() > 0 && ! $this->selectedActivityId) {
            $this->selectedActivityId = 'tab-' . $activities->first()->id;
        }

        return [
            'users' => $users,
            'activities' => $activities,
        ];
    }
}; ?>

<div>

    <!-- HEADER -->
    <x-header title="Activity" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Create" link="/activity/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- FILTERS -->
    <div class="flex flex-col lg:flex-row gap-5 mb-10 items-end">
        @if(auth()->user()->role_id == 1)
        <div class="w-full lg:w-80">
            <x-select label="Select User" wire:model.live="selectedUserId" :options="$users" icon="o-user" placeholder="Choose a user" inline />
        </div>
        @endif
        <div class="w-full lg:w-64">
            <x-datetime label="View Date" wire:model.live="selectedDate" icon="o-calendar" inline />
        </div>
    </div>

    <!-- ACTIVITY BOARD (Explorer View) -->
    <div class="max-w-7xl mx-auto">
        @if($activities->count() > 0)
        <div class="flex flex-col lg:flex-row gap-0 bg-base-100 rounded-xl shadow-sm border border-base-200 overflow-hidden min-h-[600px]">
            {{-- Left: Activity List --}}
            <div class="w-full lg:w-80 border-b lg:border-b-0 lg:border-r border-base-200 bg-base-200/20 flex flex-col pt-2 overflow-y-auto max-h-[600px] scrollbar-hide">
                @foreach($activities as $item)
                <button
                    wire:click="$set('selectedActivityId', 'tab-{{ $item->id }}')"
                    class="px-8 py-6 text-left transition-all border-l-4 {{ $selectedActivityId == 'tab-'.$item->id ? 'bg-base-100 border-primary shadow-sm' : 'border-transparent hover:bg-base-100/30 opacity-60 hover:opacity-100' }}">
                    <span class="block text-xs font-black uppercase tracking-widest {{ $selectedActivityId == 'tab-'.$item->id ? 'text-primary' : 'text-base-content/60' }} truncate mb-1">
                        {{ $item->title }}
                    </span>
                    <div class="flex items-center gap-2 mt-1">
                        <x-badge value="{{ $item->project->name ?? 'Internal' }}" class="badge-ghost font-bold text-[9px] px-2" />
                    </div>
                </button>
                @endforeach
            </div>

            {{-- Right: Activity Detail --}}
            <div class="flex-1 p-8 lg:p-16 bg-base-100 overflow-y-auto max-h-[600px]">
                @php
                $selectedItem = $activities->firstWhere('id', (int) str_replace('tab-', '', (string) $selectedActivityId));
                @endphp

                @if($selectedItem)
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-8 mb-12">
                    <div>
                        <h1 class="text-4xl lg:text-5xl font-black tracking-tighter mb-6 leading-none">{{ $selectedItem->title }}</h1>
                        <div class="flex items-center gap-4">
                            <x-badge value="{{ $selectedItem->project->name ?? 'Internal Project' }}" class="badge-primary font-black uppercase text-[10px] tracking-[0.2em] px-4 py-3" />
                            <span class="text-xs font-bold text-base-content/30 uppercase tracking-widest">{{ $selectedItem->created_at?->format('F d, Y') }}</span>
                        </div>
                    </div>

                    @if($selectedItem->user_id == auth()->id())
                    <div class="flex gap-2 shrink-0">
                        <x-button icon="o-pencil-square" link="/activity/{{ $selectedItem->id }}/edit" label="Edit" class="btn-outline btn-sm rounded-xl font-black uppercase text-[10px] tracking-widest" />
                        <x-button icon="o-trash" wire:click="delete({{ $selectedItem->id }})" wire:confirm="Are you sure you want to delete this entry?" label="Delete" class="btn-error btn-outline btn-sm rounded-xl font-black uppercase text-[10px] tracking-widest" />
                    </div>
                    @endif
                </div>

                <article class="prose prose-base lg:prose-lg max-w-none prose-headings:font-black prose-p:text-base-content/70 prose-a:text-primary EasyMDEContainer">
                    {!! Str::markdown($selectedItem->description ?? '_No description provided._') !!}
                </article>
                @endif
            </div>
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-40 bg-base-100 rounded-3xl border border-base-200 border-dashed">
            <x-icon name="o-folder-open" class="w-20 h-20 mb-6 text-base-content/10" />
            <p class="text-xl font-black uppercase tracking-[0.2em] text-base-content/20">No activities found</p>
            <p class="text-xs mt-2 font-bold uppercase tracking-widest text-base-content/10">Try selecting another user or date</p>
        </div>
        @endif
    </div>
</div>