@php
    use App\Support\Format;
    use App\Support\Reference;
@endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Working Paper"
            description="Monthly obligation working paper. RC Name and Account Title are resolved by VLOOKUP against the RC and UACS reference sheets; Unpaid = Amount − Payment.">
            <x-slot:action>
                <x-button variant="secondary"><x-icon name="download" class="size-4" /> Export</x-button>
            </x-slot:action>
        </x-page-title>

        <x-card class="bg-brand-50 border-brand-200">
            <div class="flex items-start gap-3 p-4 text-[13px]">
                <x-icon name="function-square" class="size-4 text-brand-600 mt-0.5 shrink-0" />
                <div class="text-brand-800">
                    <span class="font-semibold">Live lookups:</span> RC Name =
                    <span class="font-mono">VLOOKUP(RC Code, RC!A:B, 2, FALSE)</span> · Account Title =
                    <span class="font-mono">VLOOKUP(Object Code, UACS!A:B, 2, FALSE)</span>. Blank object codes resolve to
                    <span class="font-mono">#N/A</span>, matching the source workbook.
                </div>
            </div>
        </x-card>

        <x-card>
            <form method="GET" class="flex flex-wrap items-center gap-3 p-4 border-b border-line">
                <div class="w-36">
                    <x-select name="month" onchange="this.form.submit()">
                        @foreach ($months as $m)
                            <option value="{{ $m }}" @selected($month === $m)>{{ $m }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class="w-full sm:w-72">
                    <x-search-input :value="$query" placeholder="Search OBR#, payee, object…" />
                </div>
                <x-button size="sm" type="submit" variant="secondary">Search</x-button>
            </form>

            @if ($rows->isEmpty())
                <x-empty-state
                    icon="table-2"
                    title="No data available"
                    message="Obligation rows will appear here once OBRs are recorded or imported for the selected month." />
            @else
                <x-table>
                    <thead>
                        <tr>
                            <x-th>OBR Date</x-th>
                            <x-th>OBR No.</x-th>
                            <x-th>Payee</x-th>
                            <x-th>RC Name</x-th>
                            <x-th>Account Title</x-th>
                            <x-th right>Current</x-th>
                            <x-th right>Continuing</x-th>
                            <x-th right>Payment</x-th>
                            <x-th right>Unpaid</x-th>
                            <x-th>DV / Check</x-th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr class="hover:bg-canvas transition-colors">
                                <x-td>{{ Format::shortDate($row->obr_date) }}</x-td>
                                <x-td mono>{{ $row->obr_no }}</x-td>
                                <x-td>{{ $row->payee }}</x-td>
                                <x-td>{{ Reference::lookupRcName($row->rc_code) }}</x-td>
                                <x-td>
                                    <div class="font-mono text-[12px] text-ink-soft">{{ $row->object_code }}</div>
                                    <div>{{ Reference::lookupUacsTitle($row->object_code) }}</div>
                                </x-td>
                                <x-td right>{{ Format::amt($row->amount_current) }}</x-td>
                                <x-td right>{{ Format::amt($row->amount_continuing) }}</x-td>
                                <x-td right>{{ Format::amt($row->payment) }}</x-td>
                                <x-td right>{{ Format::amt($row->unpaidCurrent() + $row->unpaidContinuing()) }}</x-td>
                                <x-td>
                                    <div class="text-[12px] text-ink-mute">{{ $row->dv_no }}</div>
                                    <div class="text-[12px] text-ink-mute">{{ $row->check_no }}</div>
                                </x-td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-canvas font-semibold">
                            <x-td>TOTAL</x-td>
                            <x-td /><x-td /><x-td /><x-td />
                            <x-td right>{{ Format::amt($rows->sum('amount_current')) }}</x-td>
                            <x-td right>{{ Format::amt($rows->sum('amount_continuing')) }}</x-td>
                            <x-td right>{{ Format::amt($rows->sum('payment')) }}</x-td>
                            <x-td right>{{ Format::amt($rows->sum(fn ($r) => $r->unpaidCurrent() + $r->unpaidContinuing())) }}</x-td>
                            <x-td />
                        </tr>
                    </tfoot>
                </x-table>
            @endif
        </x-card>
    </div>
</x-layouts.app>
