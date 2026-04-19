<?php

use App\Models\Project;
use App\Models\Timesheet;
use Carbon\Carbon;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    // Form properties
    #[Rule('required|min:3')]
    public string $title = '';

    #[Rule('required')]
    public ?int $project_id = null;

    #[Rule('required|date')]
    public string $date = '';

    #[Rule('nullable')]
    public ?string $description = null;

    public function mount(): void
    {
        $this->date = date('Y-m-d');
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['user_id'] = auth()->id();

        Timesheet::create($data);

        $this->reset(['title', 'project_id', 'description']);
        $this->date = date('Y-m-d');

        $this->success('Timesheet saved!', position: 'toast-bottom');
    }

    public function with(): array
    {
        // Get timesheets for the last 7 days
        $timesheets = Timesheet::where('user_id', auth()->id())
            ->where('date', '>=', Carbon::now()->subDays(6)->toDateString())
            ->with('project')
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('date');

        // Ensure we have a continuous range of dates
        $dates = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $dates->put($date, $timesheets->get($date, collect()));
        }

        return [
            'projects' => Project::where('is_active', true)->get(),
            'groupedTimesheets' => $dates,
        ];
    }
}; ?>

<div>
    <x-header title="Dashboard" separator progress-indicator>
        <x-slot:middle class="hidden lg:flex">
             Welcome back, {{ auth()->user()->name }}!
        </x-slot:middle>
    </x-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        {{-- Left: Daily Lists --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Recent Activities</h2>
                    <p class="text-xs text-base-content/50 uppercase font-bold tracking-widest mt-1">Your logs from the past week</p>
                </div>
                <div class="flex gap-2">
                    <x-button icon="o-chevron-left" class="btn-circle btn-outline btn-sm" />
                    <x-button icon="o-chevron-right" class="btn-circle btn-outline btn-sm" />
                </div>
            </div>

            <div class="flex gap-4 overflow-x-auto pb-6 scrollbar-hide">
                @foreach($groupedTimesheets as $date => $items)
                    <x-card class="min-w-[300px] max-w-[300px] bg-base-100/50 backdrop-blur-sm border-base-200 hover:border-primary/30 transition-all group shadow-none border">
                        <x-slot:title>
                            <div class="flex justify-between items-center border-b border-base-content/5 pb-3">
                                <span class="text-xs font-black uppercase tracking-widest {{ $date == date('Y-m-d') ? 'text-primary' : 'text-base-content/40' }}">
                                    @if($date == date('Y-m-d')) Today
                                    @elseif($date == date('Y-m-d', strtotime('-1 day'))) Yesterday
                                    @else {{ \Carbon\Carbon::parse($date)->format('l, d M') }}
                                    @endif
                                </span>
                                <span class="badge badge-sm {{ $items->count() > 0 ? 'badge-primary' : 'badge-ghost opacity-30' }}">
                                    {{ $items->count() }}
                                </span>
                            </div>
                        </x-slot:title>

                        <div class="space-y-4 pt-2">
                            @forelse($items as $item)
                                <div class="flex gap-3 relative group/item">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary mt-1.5 shrink-0 shadow-[0_0_8px_rgba(var(--p),0.5)]"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold truncate leading-tight">{{ $item->title }}</p>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <x-icon name="o-cube" class="w-3 h-3 text-base-content/30" />
                                            <p class="text-[10px] uppercase font-black text-base-content/30 tracking-wider truncate">
                                                {{ $item->project->name ?? 'Individual Tasks' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-8 opacity-20 sepia grayscale">
                                    <x-icon name="o-clock" class="w-8 h-8 mb-2" />
                                    <p class="text-xs font-bold uppercase tracking-widest italic text-center">Ghost Town</p>
                                </div>
                            @endforelse
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>

        {{-- Right: Input Form --}}
        <div class="lg:col-span-1 sticky top-8">
            <x-card title="Record Activity" subtitle="Quickly log your work" shadow separator class="border-t-4 border-primary">
                 <x-form wire:submit="save">
                    <x-input label="Activity Title" wire:model="title" placeholder="Debugging auth issue..." icon="o-pencil-square" inline />
                    <x-select label="Target Project" wire:model="project_id" :options="$projects" placeholder="Select Project" icon="o-cube" inline />
                    <x-datetime label="Log Date" wire:model="date" icon="o-calendar" inline />
                    <x-markdown label="Description" wire:model="description" class="mt-2" />

                    <x-slot:actions>
                        <x-button label="Clear" class="btn-ghost btn-sm" @click="$wire.reset(['title', 'project_id', 'description'])" />
                        <x-button label="Submit Entry" icon="o-cloud-arrow-up" class="btn-primary" type="submit" spinner="save" />
                    </x-slot:actions>
                 </x-form>
            </x-card>
        </div>
    </div>
</div>