@php use App\Support\Format; @endphp

<x-layouts.app>
    <div class="space-y-6">
        <x-page-title :title="$cfg['title']" :description="$cfg['description']">
            <x-slot:action>
                <x-button :href="route($cfg['routePrefix'].'.create')">
                    <x-icon name="plus" class="size-4" /> {{ $cfg['newLabel'] }}
                </x-button>
            </x-slot:action>
        </x-page-title>

        <x-card>
            <form method="GET" class="flex flex-wrap items-center gap-3 p-4 border-b border-line">
                <div class="w-full sm:w-72">
                    <x-search-input :value="$query" placeholder="Search serial, payee, object code…" />
                </div>
                <div class="w-40">
                    <x-select name="status" onchange="this.form.submit()">
                        <option value="all" @selected($status === 'all')>All statuses</option>
                        @foreach ($cfg['statuses'] as $s)
                            <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
                        @endforeach
                    </x-select>
                </div>
                <x-button size="sm" type="submit" variant="secondary">Search</x-button>
            </form>

            @if ($documents->isEmpty())
                <x-empty-state
                    :icon="$cfg['icon']"
                    :title="$totalOfKind === 0 ? 'No '.strtolower($cfg['noun']).' records yet' : 'No matching records'"
                    :message="$totalOfKind === 0
                        ? 'Create a '.$cfg['noun'].' to record it here.'
                        : 'Adjust your search or status filter to see records.'">
                    @if ($totalOfKind === 0)
                        <x-slot:action>
                            <x-button :href="route($cfg['routePrefix'].'.create')">
                                <x-icon name="plus" class="size-4" /> {{ $cfg['newLabel'] }}
                            </x-button>
                        </x-slot:action>
                    @endif
                </x-empty-state>
            @else
                <x-table>
                    <thead>
                        <tr>
                            <x-th>Serial No.</x-th>
                            <x-th>Date</x-th>
                            <x-th>Payee / Office</x-th>
                            <x-th right>Total Amount</x-th>
                            <x-th right>Remaining Balance</x-th>
                            <x-th>Status</x-th>
                            <x-th />
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr class="hover:bg-canvas transition-colors">
                                <x-td mono>{{ $document->serial }}</x-td>
                                <x-td>{{ Format::shortDate($document->date) }}</x-td>
                                <x-td>
                                    <div class="font-medium text-ink">{{ $document->payee_name ?: '—' }}</div>
                                    @if ($document->office)
                                        <div class="text-[12px] text-ink-mute">{{ $document->office }}</div>
                                    @endif
                                </x-td>
                                <x-td right>{{ Format::peso($document->total()) }}</x-td>
                                <x-td right class="{{ $document->remainingBalance() < 0 ? 'text-danger' : '' }}">
                                    {{ Format::peso($document->remainingBalance()) }}
                                </x-td>
                                <x-td><x-status-badge :status="$document->status" /></x-td>
                                <x-td right>
                                    <x-button size="sm" variant="secondary"
                                              :href="route($cfg['routePrefix'].'.show', $document)">View</x-button>
                                </x-td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>

                <div class="px-5 py-3 border-t border-line text-[13px] text-ink-mute">
                    {{ $documents->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.app>
