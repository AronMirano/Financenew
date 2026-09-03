@php use App\Support\Format; @endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Deductions"
            description="Annualized mandatory deductions remitted to GSIS, Pag-IBIG, PhilHealth, and BIR.">
            <x-slot:action>
                <x-button variant="secondary"><x-icon name="download" class="size-4" /> Export</x-button>
            </x-slot:action>
        </x-page-title>

        @if ($records->isEmpty())
            <x-card>
                <x-empty-state
                    title="No data available"
                    message="Deduction schedules will be computed automatically once salary records are imported." />
            </x-card>
        @else
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-stat label="GSIS - PS share" :value="Format::peso($totals['gsis_ps'])" accent />
                <x-stat label="Pag-IBIG" :value="Format::peso($totals['pagibig'])" />
                <x-stat label="PhilHealth" :value="Format::peso($totals['philhealth'])" />
                <x-stat label="Withholding tax (BIR)" :value="Format::peso($totals['withholding_tax'])" />
            </div>

            <x-card>
                <x-card-header title="Per-employee annualized deductions"
                               subtitle="Monthly deduction × 12" />
                <x-table>
                    <thead>
                        <tr>
                            <x-th>Employee</x-th>
                            <x-th>Office</x-th>
                            <x-th right>GSIS</x-th>
                            <x-th right>Pag-IBIG</x-th>
                            <x-th right>PhilHealth</x-th>
                            <x-th right>Withholding tax</x-th>
                            <x-th right>Total</x-th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                            @php $schedule = $record->annualize(); @endphp
                            <tr class="hover:bg-canvas transition-colors">
                                <x-td>
                                    <div class="font-medium text-ink">{{ $record->name }}</div>
                                    <div class="text-[12px] text-ink-mute font-mono">{{ $record->employee_id }}</div>
                                </x-td>
                                <x-td>{{ $record->office }}</x-td>
                                <x-td right>{{ Format::amt($record->gsis_ps * 12) }}</x-td>
                                <x-td right>{{ Format::amt($record->pagibig * 12) }}</x-td>
                                <x-td right>{{ Format::amt($record->philhealth * 12) }}</x-td>
                                <x-td right>{{ Format::amt($record->withholding_tax * 12) }}</x-td>
                                <x-td right>{{ Format::amt($schedule['annualDeductions']) }}</x-td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-canvas font-semibold">
                            <x-td>TOTAL</x-td>
                            <x-td />
                            <x-td right>{{ Format::amt($totals['gsis_ps']) }}</x-td>
                            <x-td right>{{ Format::amt($totals['pagibig']) }}</x-td>
                            <x-td right>{{ Format::amt($totals['philhealth']) }}</x-td>
                            <x-td right>{{ Format::amt($totals['withholding_tax']) }}</x-td>
                            <x-td right>{{ Format::amt($grandTotal) }}</x-td>
                        </tr>
                    </tfoot>
                </x-table>
            </x-card>
        @endif
    </div>
</x-layouts.app>
