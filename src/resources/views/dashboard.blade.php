<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Dashboard"
            description="MMSU Financial Management System — Budget &amp; Finance." />

        @if ($hasData)
            <div class="grid gap-4 sm:grid-cols-3">
                <x-stat label="Obligation Requests" :value="number_format($obligationCount)" icon="receipt" accent
                        hint="OBRs recorded (Appendix 11)" />
                <x-stat label="Budget Utilizations" :value="number_format($utilizationCount)" icon="scroll-text"
                        hint="BURs recorded (Appendix 14)" />
                <x-stat label="Salary Records" :value="number_format($salaryCount)" icon="users"
                        hint="Employees in the annualization roster" />
            </div>
        @else
            <x-card>
                <x-empty-state
                    icon="database"
                    title="No data available"
                    message="Import your Excel files or add records to begin using the system.">
                    <x-slot:action>
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <x-button :href="route('pre.ppmp')">
                                <x-icon name="upload" class="size-4" /> Import PPMP
                            </x-button>
                            <x-button variant="secondary" :href="route('pre.submit')">
                                Submit PRE <x-icon name="arrow-up-right" class="size-4" />
                            </x-button>
                        </div>
                    </x-slot:action>
                </x-empty-state>
            </x-card>
        @endif
    </div>
</x-layouts.app>
