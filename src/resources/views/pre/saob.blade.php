@php use App\Support\Format; @endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="SAOB"
            description="Statement of Allotments, Obligations and Balances. Adjusted Appropriations = 1±3±4 · Adjusted Allotment = 2±3±4 · Unreleased = 5−6 · Unobligated = 6−8 · Disbursements = 8−10.">
            <x-slot:action>
                <x-button variant="secondary"><x-icon name="download" class="size-4" /> Export</x-button>
            </x-slot:action>
        </x-page-title>

        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex bg-neutral-bg rounded-lg p-0.5 border border-line">
                @foreach (['current', 'continuing'] as $b)
                    <a href="{{ request()->fullUrlWithQuery(['banner' => $b]) }}"
                       class="px-3 h-8 inline-flex items-center rounded-md text-[13px] font-medium capitalize transition-colors {{ $banner === $b ? 'bg-surface text-brand-700 shadow-sm' : 'text-ink-mute' }}">
                        {{ $b }} Appropriations
                    </a>
                @endforeach
            </div>
            @if ($sheet['mooe_only'])
                <x-badge tone="info">MOOE-only sheet</x-badge>
            @endif
        </div>

        <x-card>
            <div class="px-4 pt-3 border-b border-line">
                <x-tabs :tabs="collect($sheets)->map(fn ($s) => ['id' => $s['key'], 'label' => $s['code']])->all()"
                        :active="$sheet['key']" param="sheet" />
            </div>

            <div class="px-5 py-3 border-b border-line bg-canvas">
                <div class="text-[13px] font-semibold text-ink">{{ $sheet['code'] }} — {{ $sheet['name'] }}</div>
                <div class="text-[12px] text-ink-mute capitalize">{{ $banner }} appropriations · FY {{ now()->year }}</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[13px] border-collapse">
                    <thead>
                        <tr>
                            <x-th class="min-w-[280px]">P/A/P / Allotment Class / Object of Expenditure</x-th>
                            @foreach ($columns as $c)
                                <x-th right class="whitespace-nowrap">
                                    <span class="text-ink-soft">{{ $c['n'] }}.</span> {{ $c['label'] }}
                                </x-th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr class="hover:bg-canvas transition-colors">
                                <x-td>
                                    <div class="font-medium text-ink">{{ $row['line']->label }}</div>
                                    <div class="text-[12px] text-ink-mute">
                                        {{ $row['line']->cls }}
                                        @if ($row['line']->object_code)
                                            · <span class="font-mono">{{ $row['line']->object_code }}</span>
                                        @endif
                                    </div>
                                </x-td>
                                @foreach ($columns as $c)
                                    <x-td right>{{ Format::amt($row['row'][$c['key']] ?? 0) }}</x-td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 1 }}">
                                    <x-empty-state
                                        compact
                                        title="No data available"
                                        message="Allotment and obligation figures for this program will appear once records are entered or imported." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($rows) > 0)
                        <tfoot>
                            <tr class="bg-canvas font-semibold">
                                <x-td>TOTAL</x-td>
                                @foreach ($columns as $c)
                                    <x-td right>{{ Format::amt($totals[$c['key']] ?? 0) }}</x-td>
                                @endforeach
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="px-5 py-4 border-t border-line flex items-end justify-between gap-4">
                <p class="text-[12px] text-ink-mute max-w-md">
                    Prepared in conformity with DBM budget accountability requirements.
                </p>
                <div class="text-right">
                    <div class="border-t border-ink pt-1 inline-block text-left">
                        <div class="font-semibold text-ink text-[13px]">{{ strtoupper(auth()->user()?->name ?? '') }}</div>
                        <div class="text-[12px] text-ink-mute">{{ auth()->user()?->position }}</div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.app>
