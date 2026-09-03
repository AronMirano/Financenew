<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="PRE Overview"
            description="Program of Receipts and Expenditures across all responsibility centers for Fiscal Year {{ now()->year }}.">
            <x-slot:action>
                <x-button :href="route('pre.submit')">
                    <x-icon name="plus" class="size-4" /> Submit PRE
                </x-button>
            </x-slot:action>
        </x-page-title>

        <x-card>
            <x-empty-state
                icon="database"
                title="No data available"
                message="Submit a PRE or import your Excel files to populate program allotments and utilization here.">
                <x-slot:action>
                    <x-button :href="route('pre.submit')">
                        <x-icon name="plus" class="size-4" /> Submit PRE
                    </x-button>
                </x-slot:action>
            </x-empty-state>
        </x-card>
    </div>
</x-layouts.app>
