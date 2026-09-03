@php use App\Support\Format; @endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Monitoring"
            description="Utilization of funds and consolidated PPMP tracking. Column terminology follows the source workbooks verbatim — the Utilization sheet uses MODE, the consolidation sheets use MOOE.">
            <x-slot:action>
                <x-button variant="secondary"><x-icon name="download" class="size-4" /> Export</x-button>
            </x-slot:action>
        </x-page-title>

        <x-card>
            <div class="px-4 pt-3 border-b border-line">
                <x-tabs :tabs="$tabs" :active="$tab" />
            </div>

            @if ($tab === 'utilization')
                <div class="px-5 py-2.5 border-b border-line bg-canvas text-[12px] text-ink-mute">
                    Utilization of Fund · <span class="font-mono">TOTAL = PS + MODE + CO</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-[13px] border-collapse">
                        <thead>
                            <tr>
                                <x-th class="min-w-[200px]">Particulars</x-th>
                                <x-th right>2022 Balance</x-th>
                                <x-th right>Receipts FHE</x-th>
                                <x-th right>2023 Collections</x-th>
                                <x-th right>PRE</x-th>
                                <x-th right class="border-l border-line">Util. PS</x-th>
                                <x-th right>Util. MODE</x-th>
                                <x-th right>Util. CO</x-th>
                                <x-th right>Util. TOTAL</x-th>
                                <x-th right class="border-l border-line">Disb. Total</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($utilizationRows as $row)
                                <tr class="hover:bg-canvas transition-colors">
                                    <x-td>
                                        <div class="font-medium text-ink">{{ $row->particulars }}</div>
                                        @if ($row->section)
                                            <div class="text-[12px] text-ink-mute">{{ $row->section }}</div>
                                        @endif
                                    </x-td>
                                    <x-td right>{{ Format::amt($row->balance_2022) }}</x-td>
                                    <x-td right>{{ Format::amt($row->receipts_fhe) }}</x-td>
                                    <x-td right>{{ Format::amt($row->collections_2023) }}</x-td>
                                    <x-td right>{{ Format::amt($row->pre) }}</x-td>
                                    <x-td right class="border-l border-line">{{ Format::amt($row->util_ps) }}</x-td>
                                    <x-td right>{{ Format::amt($row->util_mode) }}</x-td>
                                    <x-td right>{{ Format::amt($row->util_co) }}</x-td>
                                    <x-td right>{{ Format::amt($row->utilizationTotal()) }}</x-td>
                                    <x-td right class="border-l border-line">{{ Format::amt($row->disbursementTotal()) }}</x-td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <x-empty-state compact title="No data available"
                                                       message="Fund utilization figures will appear once records are entered or imported." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-5 py-2.5 border-b border-line bg-canvas text-[12px] text-ink-mute flex items-center justify-between gap-3 flex-wrap">
                    <span>
                        Consolidated PPMP — {{ $kindLabel }} ·
                        <span class="font-mono">Total = PS + MOOE + CO + Contingency + Re-alignment</span>
                    </span>
                    <x-badge tone="neutral">
                        {{ $consoRows->isEmpty() ? 'No records' : $consoRows->count().' records' }}
                    </x-badge>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-[13px] border-collapse">
                        <thead>
                            <tr>
                                <x-th>PPMP</x-th>
                                <x-th>Unit</x-th>
                                <x-th class="min-w-[240px]">Item</x-th>
                                <x-th right>PS</x-th>
                                <x-th right>MOOE</x-th>
                                <x-th right>CO</x-th>
                                <x-th right>Contingency</x-th>
                                <x-th right>Re-alignment</x-th>
                                <x-th right>Total</x-th>
                                <x-th>Request</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($consoRows as $row)
                                <tr class="hover:bg-canvas transition-colors">
                                    <x-td>{{ $row->ppmp }}</x-td>
                                    <x-td>{{ $row->unit }}</x-td>
                                    <x-td>{{ $row->item }}</x-td>
                                    <x-td right>{{ Format::amt($row->ps) }}</x-td>
                                    <x-td right>{{ Format::amt($row->mooe) }}</x-td>
                                    <x-td right>{{ Format::amt($row->co) }}</x-td>
                                    <x-td right>{{ Format::amt($row->contingency) }}</x-td>
                                    <x-td right>{{ Format::amt($row->realignment) }}</x-td>
                                    <x-td right>{{ Format::amt($row->total()) }}</x-td>
                                    <x-td>{{ $row->request }}</x-td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <x-empty-state compact title="No data available"
                                                       message="Consolidated PPMP line items will appear once records are entered or imported." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.app>
