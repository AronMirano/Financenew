@php
    use App\Support\Format;
    use App\Support\Reference;
@endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title
            title="Expenditures"
            description="Obligations and utilizations aggregated by UACS object of expenditure.">
            <x-slot:action>
                <x-button variant="secondary"><x-icon name="download" class="size-4" /> Export</x-button>
            </x-slot:action>
        </x-page-title>

        <x-card>
            <form method="GET" class="flex flex-wrap items-center gap-3 p-4 border-b border-line">
                <div class="w-56">
                    <x-select name="cls" onchange="this.form.submit()">
                        <option value="all" @selected($cls === 'all')>All allotment classes</option>
                        @foreach (Reference::EXPENSE_CLASSES as $code => $name)
                            <option value="{{ $code }}" @selected($cls === $code)>{{ $code }} — {{ $name }}</option>
                        @endforeach
                    </x-select>
                </div>
                <noscript><x-button size="sm" type="submit" variant="secondary">Filter</x-button></noscript>
            </form>

            @if ($rows->isEmpty())
                <x-empty-state
                    title="No data available"
                    message="Aggregated expenditures will appear once obligations or utilizations are recorded." />
            @else
                <x-table>
                    <thead>
                        <tr>
                            <x-th>UACS Object Code</x-th>
                            <x-th>Account Title</x-th>
                            <x-th>Class</x-th>
                            <x-th right>Lines</x-th>
                            <x-th right>Total</x-th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr class="hover:bg-canvas transition-colors">
                                <x-td mono>{{ $row['object_code'] }}</x-td>
                                <x-td>{{ $row['title'] }}</x-td>
                                <x-td>
                                    @if ($row['cls'])
                                        <x-badge tone="neutral">{{ $row['cls'] }}</x-badge>
                                    @else
                                        <span class="text-ink-mute">—</span>
                                    @endif
                                </x-td>
                                <x-td right>{{ number_format($row['line_count']) }}</x-td>
                                <x-td right>{{ Format::peso($row['total']) }}</x-td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-canvas font-semibold">
                            <x-td>TOTAL</x-td>
                            <x-td />
                            <x-td />
                            <x-td right>{{ number_format($rows->sum('line_count')) }}</x-td>
                            <x-td right>{{ Format::peso($rows->sum('total')) }}</x-td>
                        </tr>
                    </tfoot>
                </x-table>
            @endif
        </x-card>
    </div>
</x-layouts.app>
