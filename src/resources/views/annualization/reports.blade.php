<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Annualization Reports"
            description="Generate personnel services and deduction reports." />

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($reports as $report)
                <x-card class="p-5 flex items-start gap-4">
                    <div class="size-9 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                        <x-icon name="file-text" class="size-4" />
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-[14px] font-semibold text-ink">{{ $report['name'] }}</h3>
                            <x-badge tone="neutral">{{ $report['code'] }}</x-badge>
                        </div>
                        <p class="text-[12px] text-ink-mute mt-1">{{ $report['desc'] }}</p>
                        <div class="flex gap-2 mt-3">
                            <form method="POST" action="{{ route('ann.reports.generate') }}">
                                @csrf
                                <input type="hidden" name="code" value="{{ $report['code'] }}">
                                <input type="hidden" name="mode" value="generate">
                                <x-button size="sm" type="submit">
                                    <x-icon name="check-circle-2" class="size-4" /> Generate
                                </x-button>
                            </form>
                            <form method="POST" action="{{ route('ann.reports.generate') }}">
                                @csrf
                                <input type="hidden" name="code" value="{{ $report['code'] }}">
                                <input type="hidden" name="mode" value="download">
                                <x-button size="sm" type="submit" variant="secondary">
                                    <x-icon name="download" class="size-4" />
                                    <span class="sr-only">Download {{ $report['code'] }}</span>
                                </x-button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
</x-layouts.app>
