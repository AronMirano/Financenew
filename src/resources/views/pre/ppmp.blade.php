<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="PPMP Import"
            description="Import Project Procurement Management Plan line items from a spreadsheet and map them to UACS object codes." />

        <div class="grid gap-6 lg:grid-cols-3">
            <x-card class="lg:col-span-1">
                <x-card-header title="Upload file" subtitle="Accepts .xlsx / .csv" />
                <div class="p-5">
                    <div class="border-2 border-dashed border-line rounded-xl p-8 text-center hover:border-brand-300 transition-colors">
                        <x-icon name="upload" class="size-8 text-ink-mute mx-auto mb-3" />
                        <div class="text-sm font-medium text-ink">Drop PPMP file here</div>
                        <div class="text-[12px] text-ink-mute mt-1 mb-4">or browse from your computer</div>
                        <x-button size="sm" variant="secondary">Select file</x-button>
                    </div>
                </div>
            </x-card>

            <x-card class="lg:col-span-2">
                <x-card-header title="Preview &amp; mapping" subtitle="Upload a file to preview" />
                <x-empty-state
                    icon="layout-grid"
                    title="No file uploaded yet"
                    message="Parsed PPMP rows will appear here for review and UACS mapping before import." />
            </x-card>
        </div>
    </div>
</x-layouts.app>
