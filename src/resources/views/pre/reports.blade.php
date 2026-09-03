<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="PRE Reports"
            description="Generate DBM budget accountability reports (FARs / BARs) from recorded transactions." />

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($reports as $report)
                <x-card class="p-5 flex flex-col">
                    <div class="flex items-start justify-between">
                        <div class="size-9 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                            <x-icon name="file-text" class="size-4" />
                        </div>
                        <x-badge tone="neutral">{{ $report['code'] }}</x-badge>
                    </div>
                    <h3 class="text-[14px] font-semibold text-ink mt-3 leading-snug">{{ $report['name'] }}</h3>
                    <p class="text-[12px] text-ink-mute mt-1 flex-1">{{ $report['desc'] }}</p>
                    <div class="flex gap-2 mt-4">
                        <form method="POST" action="{{ route('pre.reports.generate') }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="code" value="{{ $report['code'] }}">
                            <input type="hidden" name="mode" value="generate">
                            <x-button size="sm" type="submit" class="w-full">Generate</x-button>
                        </form>
                        <form method="POST" action="{{ route('pre.reports.generate') }}">
                            @csrf
                            <input type="hidden" name="code" value="{{ $report['code'] }}">
                            <input type="hidden" name="mode" value="download">
                            <x-button size="sm" type="submit" variant="secondary">
                                <x-icon name="download" class="size-4" />
                                <span class="sr-only">Download {{ $report['code'] }}</span>
                            </x-button>
                        </form>
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
</x-layouts.app>
