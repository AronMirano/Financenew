@php use App\Support\Format; @endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Annualization Analytics"
            description="Distribution of annualized personnel services and deductions." />

        @if ($byOffice->isEmpty())
            <x-card>
                <x-empty-state
                    icon="bar-chart-3"
                    title="No data available"
                    message="Charts will be generated automatically once salary records are imported." />
            </x-card>
        @else
            @php $max = (float) $byOffice->max('annual_basic') ?: 1; @endphp
            <x-card>
                <x-card-header title="Annualized basic salary by office"
                               subtitle="Monthly basic × 12, grouped by responsibility center" />
                <div class="p-5 space-y-4">
                    @foreach ($byOffice as $row)
                        <div>
                            <div class="flex items-baseline justify-between gap-4 text-[13px] mb-1">
                                <span class="text-ink font-medium">{{ $row->office ?: 'Unassigned' }}</span>
                                <span class="text-ink-mute tnum">
                                    {{ Format::peso($row->annual_basic) }}
                                    · {{ number_format($row->headcount) }} staff
                                </span>
                            </div>
                            <x-progress-bar :value="$row->annual_basic / $max" />
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif
    </div>
</x-layouts.app>
