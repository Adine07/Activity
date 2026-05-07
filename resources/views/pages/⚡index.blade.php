<?php

use App\Models\Project;
use App\Models\Activity;
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
    public string $description = '';

    public int $editorKey = 0;
    public Illuminate\Support\Collection $projectsSearchable;

    public function mount(): void
    {
        $this->date = date('Y-m-d\TH:i');
        $this->description = '';
        $this->search();
    }

    public function search(string $value = ''): void
    {
        $selectedOption = Project::where('id', $this->project_id)->get();

        $this->projectsSearchable = Project::query()
            ->where('is_active', true)
            ->where('name', 'like', "%$value%")
            ->take(5)
            ->orderBy('name')
            ->get()
            ->merge($selectedOption);
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['user_id'] = auth()->id();

        if (! empty($data['date'])) {
            $data['date'] = \Carbon\Carbon::parse($data['date'])->format('Y-m-d H:i:s');
        }

        Activity::create($data);

        $this->title = '';
        $this->project_id = null;
        $this->description = '';
        $this->date = date('Y-m-d\TH:i');
        $this->editorKey++;

        $this->success('Activity saved!', position: 'toast-bottom');
    }

    public function clear(): void
    {
        $this->title = '';
        $this->project_id = null;
        $this->description = '';
        $this->date = date('Y-m-d\TH:i');
        $this->editorKey++;
    }

    public function with(): array
    {
        // Get activities for the last 7 days
        $activities = Activity::where('user_id', auth()->id())
            ->where('date', '>=', Carbon::now()->subDays(6)->toDateString())
            ->with('project')
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->date)->toDateString();
            });

        // Ensure we have a continuous range of dates
        $dates = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $dates->put($date, $activities->get($date, collect()));
        }

        return [
            'groupedActivities' => $dates,
        ];
    }
}; ?>

@section('title', 'Dashboard')

<div>
    <x-header title="Dashboard" separator progress-indicator>
        <x-slot:middle class="hidden lg:flex">
            Welcome back, {{ auth()->user()->name }}!
        </x-slot:middle>
    </x-header>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Recent Activities</h2>
                    <p class="text-xs text-base-content/50 uppercase font-bold tracking-widest mt-1">Your logs from the past week</p>
                </div>
            </div>

            <div class="flex gap-4 overflow-x-auto pb-6 scrollbar-hide">
                @foreach($groupedActivities as $date => $items)
                <a href="{{ route('activity.index', ['user' => auth()->id(), 'date' => $date]) }}" wire:navigate class="block group/link">
                    <x-card
                        class="min-w-[300px] max-w-[300px] min-h-[450px] bg-base-100/50 backdrop-blur-sm border-base-200 hover:border-primary/30 transition-all group shadow-none border cursor-pointer">
                        <x-slot:title>
                            <div class="flex justify-between items-center border-b border-base-content/5 pb-3">
                                <span class="text-xs font-black uppercase tracking-widest {{ $date == date('Y-m-d') ? 'text-primary' : 'text-base-content/40' }}">
                                    @if($date == date('Y-m-d')) Today
                                    @elseif($date == date('Y-m-d', strtotime('-1 day'))) Yesterday
                                    @else {{ \Carbon\Carbon::parse($date)->format('l, d M') }}
                                    @endif
                                </span>
                                <span class="badge badge-sm {{ $items->count() > 0 ? 'badge-primary' : 'badge-ghost opacity-30 shadow-[0_0_10px_rgba(var(--p),0.2)]' }}">
                                    {{ $items->count() }}
                                </span>
                            </div>
                        </x-slot:title>

                        <div class="space-y-4 pt-2">
                            @forelse($items as $item)
                            <div class="flex gap-3 relative group/item">
                                <div class="w-2.5 h-2.5 rounded-full bg-primary mt-1.5 shrink-0 shadow-[0_0_8px_rgba(var(--p),0.5)]"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold truncate leading-tight group-hover/link:text-primary transition-colors">{{ $item->title }}</p>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <x-icon name="o-cube" class="w-3 h-3 text-base-content/30" />
                                        <p class="text-[10px] uppercase font-black text-base-content/30 tracking-wider truncate">
                                            {{ $item->project->name ?? 'Individual Tasks' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center py-8 opacity-20 sepia grayscale group-hover/link:opacity-40 transition-opacity">
                                <x-icon name="o-clock" class="w-8 h-8 mb-2" />
                                <p class="text-xs font-bold uppercase tracking-widest italic text-center">Ghost Town</p>
                            </div>
                            @endforelse
                        </div>
                    </x-card>
                </a>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-3 sticky top-8">
            <x-card title="Record Your Activity" subtitle="Quickly log your work" shadow separator class="border-t-4 border-primary">
                <x-form wire:submit="save">
                    <x-input label="Activity Title" wire:model="title" placeholder="type your activity..." icon="o-pencil-square" inline />
                    <x-choices label="Target Project" wire:model="project_id" :options="$projectsSearchable" placeholder="Select Project" icon="o-cube" inline single searchable />
                    <x-datetime label="Log Date" wire:model="date" type="datetime-local" icon="o-calendar" inline />

                    <div class="mt-2 text-base-content" wire:key="editor-wrapper-{{ $editorKey }}">
                        @php
                            $config = [
                                'spellChecker' => true,
                                'toolbar' => ['heading', 'bold', 'italic', '|', 'code', 'quote', 'ordered-list', 'unordered-list', '|', 'link', 'table'],
                            ];
                        @endphp
                        @if($editorKey % 2 == 0)
                        <x-markdown id="editor-0" label="Description" wire:model="description" :config="$config" />
                        @else
                        <x-markdown id="editor-1" label="Description" wire:model="description" :config="$config" />
                        @endif
                    </div>

                    <x-slot:actions>
                        <x-button label="Clear" class="btn-ghost btn-sm" wire:click="clear" spinner="clear" />
                        <x-button label="Submit Entry" icon="o-cloud-arrow-up" class="btn-primary" type="submit" spinner="save" />
                    </x-slot:actions>
                </x-form>
            </x-card>
        </div>
    </div>
</div>