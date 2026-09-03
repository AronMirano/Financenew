@php use App\Support\Format; @endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Salary Records"
            description="Per-employee annualized salary and deduction schedule.">
            <x-slot:action>
                <x-button variant="secondary"><x-icon name="download" class="size-4" /> Export</x-button>
            </x-slot:action>
        </x-page-title>

        <x-card>
            <form method="GET" class="flex flex-wrap items-center gap-3 p-4 border-b border-line">
                <div class="w-full sm:w-72">
                    <x-search-input :value="$query" placeholder="Search name or employee ID…" />
                </div>
                <div class="w-44">
                    <x-select name="office" onchange="this.form.submit()">
                        <option value="all" @selected($office === 'all')>All offices</option>
                        @foreach ($offices as $o)
                            <option value="{{ $o }}" @selected($office === $o)>{{ $o }}</option>
                        @endforeach
                    </x-select>
                </div>
                <x-button size="sm" type="submit" variant="secondary">Search</x-button>
            </form>

            @if ($records->isEmpty())
                <x-empty-state
                    icon="users"
                    title="No salary records"
                    message="Import a payroll register to display per-employee annualized salaries here." />
            @else
                <x-table>
                    <thead>
                        <tr>
                            <x-th>Employee</x-th>
                            <x-th>Office</x-th>
                            <x-th>SG / Step</x-th>
                            <x-th right>Monthly Basic</x-th>
                            <x-th right>Months</x-th>
                            <x-th right>Annualized Basic</x-th>
                            <x-th right>Annual Deductions</x-th>
                            <x-th right>Annual Net</x-th>
                            <x-th />
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
                                <x-td>{{ $record->sg_step }}</x-td>
                                <x-td right>{{ Format::peso($record->monthly_basic) }}</x-td>
                                <x-td right>{{ $record->months_served }}</x-td>
                                <x-td right>{{ Format::peso($schedule['annualBasic']) }}</x-td>
                                <x-td right>{{ Format::peso($schedule['annualDeductions']) }}</x-td>
                                <x-td right>{{ Format::peso($schedule['annualNet']) }}</x-td>
                                <x-td right>
                                    <x-button size="sm" variant="secondary" :href="route('ann.employee', $record)">View</x-button>
                                </x-td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>

                <div class="px-5 py-3 border-t border-line text-[13px] text-ink-mute">
                    {{ $records->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.app>
