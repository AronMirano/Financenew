@php use App\Support\Format; @endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Employee Details"
            description="Detailed annualization schedule for an individual employee." />

        @if ($roster->isNotEmpty())
            <x-card>
                <form method="GET" class="flex flex-wrap items-center gap-3 p-4">
                    <div class="w-full sm:w-96">
                        <x-select onchange="if (this.value) window.location = this.value">
                            <option value="">Select an employee…</option>
                            @foreach ($roster as $r)
                                <option value="{{ route('ann.employee', $r) }}" @selected($record?->id === $r->id)>
                                    {{ $r->name }} — {{ $r->employee_id }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                </form>
            </x-card>
        @endif

        @if ($record && $schedule)
            <div class="grid gap-6 lg:grid-cols-3">
                <x-card class="lg:col-span-2">
                    <x-card-header :title="$record->name" :subtitle="$record->position" />
                    <div class="p-5 grid gap-x-6 gap-y-2 sm:grid-cols-2 text-[13px]">
                        <x-obligations.meta label="Employee ID">
                            <span class="font-mono">{{ $record->employee_id }}</span>
                        </x-obligations.meta>
                        <x-obligations.meta label="Office">{{ $record->office ?: '—' }}</x-obligations.meta>
                        <x-obligations.meta label="Salary Grade / Step">{{ $record->sg_step ?: '—' }}</x-obligations.meta>
                        <x-obligations.meta label="Months served">{{ $record->months_served }}</x-obligations.meta>
                    </div>

                    <div class="px-5 pb-2 font-semibold text-ink text-[13px]">Annualization schedule</div>
                    <x-table>
                        <thead>
                            <tr>
                                <x-th>Component</x-th>
                                <x-th right>Monthly</x-th>
                                <x-th right>Annualized (12 mo.)</x-th>
                                <x-th right>Earned to date</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <x-td>Basic salary</x-td>
                                <x-td right>{{ Format::peso($record->monthly_basic) }}</x-td>
                                <x-td right>{{ Format::peso($schedule['annualBasic']) }}</x-td>
                                <x-td right>{{ Format::peso($schedule['earnedBasic']) }}</x-td>
                            </tr>
                            <tr>
                                <x-td>GSIS - PS share</x-td>
                                <x-td right>{{ Format::peso($record->gsis_ps) }}</x-td>
                                <x-td right>{{ Format::peso($record->gsis_ps * 12) }}</x-td>
                                <x-td right>{{ Format::peso($record->gsis_ps * $record->months_served) }}</x-td>
                            </tr>
                            <tr>
                                <x-td>Pag-IBIG</x-td>
                                <x-td right>{{ Format::peso($record->pagibig) }}</x-td>
                                <x-td right>{{ Format::peso($record->pagibig * 12) }}</x-td>
                                <x-td right>{{ Format::peso($record->pagibig * $record->months_served) }}</x-td>
                            </tr>
                            <tr>
                                <x-td>PhilHealth</x-td>
                                <x-td right>{{ Format::peso($record->philhealth) }}</x-td>
                                <x-td right>{{ Format::peso($record->philhealth * 12) }}</x-td>
                                <x-td right>{{ Format::peso($record->philhealth * $record->months_served) }}</x-td>
                            </tr>
                            <tr>
                                <x-td>Withholding tax</x-td>
                                <x-td right>{{ Format::peso($record->withholding_tax) }}</x-td>
                                <x-td right>{{ Format::peso($record->withholding_tax * 12) }}</x-td>
                                <x-td right>{{ Format::peso($record->withholding_tax * $record->months_served) }}</x-td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-canvas font-semibold">
                                <x-td>NET PAY</x-td>
                                <x-td right>{{ Format::peso($record->monthly_basic - $schedule['monthlyDeductions']) }}</x-td>
                                <x-td right>{{ Format::peso($schedule['annualNet']) }}</x-td>
                                <x-td right>{{ Format::peso($schedule['earnedNet']) }}</x-td>
                            </tr>
                        </tfoot>
                    </x-table>
                </x-card>

                <div class="space-y-4">
                    <x-stat label="Annualized Basic" :value="Format::peso($schedule['annualBasic'])" accent icon="wallet" />
                    <x-stat label="Annual Deductions" :value="Format::peso($schedule['annualDeductions'])" icon="receipt" />
                    <x-stat label="Annual Net" :value="Format::peso($schedule['annualNet'])" accent icon="bar-chart-3" />
                </div>
            </div>
        @else
            <x-card>
                <x-empty-state
                    icon="receipt"
                    title="No employee selected"
                    message="Import salary records first — individual annualization schedules will be available once employees exist." />
            </x-card>
        @endif
    </div>
</x-layouts.app>
