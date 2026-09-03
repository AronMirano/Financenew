@php use App\Support\Format; @endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Annualization Overview"
            description="Full-year projection of personnel services for FY {{ now()->year }} — annualized basic salaries, mandatory deductions, and net pay.">
            <x-slot:action>
                <x-button :href="route('ann.import')">
                    <x-icon name="upload" class="size-4" /> Import salary file
                </x-button>
            </x-slot:action>
        </x-page-title>

        @if ($count > 0)
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-stat label="Employees" :value="number_format($count)" icon="users" />
                <x-stat label="Annualized Basic" :value="Format::peso($annualBasic)" icon="wallet" accent
                        hint="Monthly basic × 12" />
                <x-stat label="Annualized Deductions" :value="Format::peso($annualDeductions)" icon="receipt"
                        hint="GSIS · Pag-IBIG · PhilHealth · BIR" />
                <x-stat label="Annualized Net" :value="Format::peso($annualNet)" icon="bar-chart-3" accent />
            </div>
        @else
            <x-card>
                <x-empty-state
                    icon="database"
                    title="No data available"
                    message="Import your plantilla or payroll register to compute annualized personnel services.">
                    <x-slot:action>
                        <x-button :href="route('ann.import')">
                            <x-icon name="upload" class="size-4" /> Import salary file
                        </x-button>
                    </x-slot:action>
                </x-empty-state>
            </x-card>
        @endif
    </div>
</x-layouts.app>
