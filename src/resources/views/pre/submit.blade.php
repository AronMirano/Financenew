@php
    use App\Support\Format;
    use App\Support\Reference;

    $items = old('items', [['object_code' => '', 'amount' => '']]);
    $total = Format::sum(array_map(fn ($i) => (float) ($i['amount'] ?? 0), $items));
@endphp

<x-layouts.app>
    <form method="POST" action="{{ route('pre.submit.store') }}" class="space-y-6">
        @csrf

        <x-page-title
            title="Submit PRE"
            description="Prepare and submit a Program of Receipts and Expenditures for a responsibility center." />

        @if ($errors->any())
            <div class="border border-danger rounded-lg px-4 py-3 text-[13px] text-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <x-card class="lg:col-span-2">
                <x-card-header title="Program details" />
                <div class="p-5 grid gap-4 sm:grid-cols-2">
                    <x-field label="Responsibility Center" required :error="$errors->first('rc')">
                        <x-select name="rc">
                            <option value="">Select RC…</option>
                            @foreach (Reference::RC_RECORDS as $r)
                                <option value="{{ $r['code'] }}" @selected(old('rc') === $r['code'])>
                                    {{ $r['acronym'] }} — {{ $r['name'] }}
                                </option>
                            @endforeach
                        </x-select>
                    </x-field>

                    <x-field label="Fund Category" required :error="$errors->first('category')">
                        <x-select name="category">
                            <option value="">Select category…</option>
                            @foreach (Reference::FUND_CATEGORIES as $code => $name)
                                <option value="{{ $code }}" @selected(old('category') === $code)>
                                    {{ $code }} — {{ $name }}
                                </option>
                            @endforeach
                        </x-select>
                    </x-field>

                    <x-field label="Period">
                        <x-select name="period">
                            @foreach ($periods as $p)
                                <option value="{{ $p }}" @selected(old('period', 'Q3 '.now()->year) === $p)>{{ $p }}</option>
                            @endforeach
                        </x-select>
                    </x-field>

                    <x-field label="Prepared by">
                        <x-input name="prepared_by" value="{{ old('prepared_by') }}" placeholder="Enter preparer name" />
                    </x-field>
                </div>

                <div class="px-5 pb-2 flex items-center justify-between">
                    <h4 class="text-[13px] font-semibold text-ink">Expenditure line items</h4>
                    <x-button size="sm" variant="secondary" data-add-line>
                        <x-icon name="plus" class="size-4" /> Add line
                    </x-button>
                </div>

                <x-table>
                    <thead>
                        <tr>
                            <x-th>UACS Object Code</x-th>
                            <x-th>Class</x-th>
                            <x-th right>Amount</x-th>
                            <x-th />
                        </tr>
                    </thead>
                    <tbody data-line-body>
                        @foreach ($items as $i => $item)
                            <tr data-line-row>
                                <x-td>
                                    <x-select name="items[{{ $i }}][object_code]" data-object-code>
                                        <option value="">Select UACS…</option>
                                        @foreach (Reference::UACS_RECORDS as $u)
                                            <option value="{{ $u['code'] }}" data-cls="{{ $u['cls'] }}"
                                                    data-title="{{ $u['title'] }}"
                                                    @selected(($item['object_code'] ?? '') === $u['code'])>
                                                {{ $u['code'] }} — {{ $u['title'] }}
                                            </option>
                                        @endforeach
                                    </x-select>
                                </x-td>
                                <x-td>
                                    <span data-class-badge class="text-ink-mute">
                                        {{ Reference::uacsClass($item['object_code'] ?? '') ?? '—' }}
                                    </span>
                                </x-td>
                                <x-td right>
                                    <x-input name="items[{{ $i }}][amount]" inputmode="decimal"
                                             value="{{ $item['amount'] ?? '' }}" placeholder="0.00"
                                             class="text-right tnum w-36 ml-auto" />
                                </x-td>
                                <x-td>
                                    <button type="button" data-remove-line
                                            class="text-ink-mute hover:text-danger p-1.5 rounded focus-ring">
                                        <x-icon name="trash-2" class="size-4" />
                                        <span class="sr-only">Remove line</span>
                                    </button>
                                </x-td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-canvas font-semibold">
                            <x-td>TOTAL</x-td>
                            <x-td />
                            <x-td right>{{ Format::peso($total) }}</x-td>
                            <x-td />
                        </tr>
                    </tfoot>
                </x-table>
            </x-card>

            <div class="space-y-6">
                <x-card>
                    <x-card-header title="Summary" />
                    <div class="p-5 space-y-3 text-[13px]">
                        @php $rc = collect(Reference::RC_RECORDS)->firstWhere('code', old('rc')); @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-ink-mute">Responsibility Center</span>
                            <span class="font-medium text-ink">{{ $rc['acronym'] ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-ink-mute">Fund Category</span>
                            <span class="font-medium text-ink">{{ old('category') ?: '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-ink-mute">Period</span>
                            <span class="font-medium text-ink">{{ old('period', 'Q3 '.now()->year) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-ink-mute">Line items</span>
                            <span class="font-medium text-ink">{{ count($items) }}</span>
                        </div>
                        <div class="border-t border-line pt-3 flex items-center justify-between">
                            <span class="text-ink-mute">Total amount</span>
                            <span class="font-semibold text-brand-700 tnum">{{ Format::peso($total) }}</span>
                        </div>
                    </div>
                </x-card>

                <div class="flex flex-col gap-2">
                    <x-button type="submit" name="action" value="submit">
                        <x-icon name="check-circle-2" class="size-4" /> Submit for review
                    </x-button>
                    <x-button type="submit" name="action" value="draft" variant="secondary">Save as draft</x-button>
                </div>
            </div>
        </div>
    </form>

    {{-- Template for JS-added line rows. --}}
    <template data-line-template>
        <tr data-line-row>
            <td class="px-3 py-2.5 border-b border-line-soft text-ink align-middle">
                <div class="relative">
                    <select name="items[__INDEX__][object_code]" data-object-code
                            class="w-full h-9 pl-3 pr-9 rounded-lg border border-line bg-surface text-sm text-ink appearance-none focus-ring focus:border-brand-500 transition-colors">
                        <option value="">Select UACS…</option>
                        @foreach (Reference::UACS_RECORDS as $u)
                            <option value="{{ $u['code'] }}" data-cls="{{ $u['cls'] }}" data-title="{{ $u['title'] }}">
                                {{ $u['code'] }} — {{ $u['title'] }}
                            </option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="size-4 text-ink-mute absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                </div>
            </td>
            <td class="px-3 py-2.5 border-b border-line-soft text-ink align-middle">
                <span data-class-badge class="text-ink-mute">—</span>
            </td>
            <td class="px-3 py-2.5 border-b border-line-soft text-ink align-middle text-right tnum">
                <input name="items[__INDEX__][amount]" inputmode="decimal" placeholder="0.00"
                       class="w-full h-9 px-3 rounded-lg border border-line bg-surface text-sm text-ink placeholder:text-ink-mute focus-ring focus:border-brand-500 transition-colors text-right tnum w-36 ml-auto">
            </td>
            <td class="px-3 py-2.5 border-b border-line-soft text-ink align-middle">
                <button type="button" data-remove-line class="text-ink-mute hover:text-danger p-1.5 rounded focus-ring">
                    <x-icon name="trash-2" class="size-4" />
                    <span class="sr-only">Remove line</span>
                </button>
            </td>
        </tr>
    </template>
</x-layouts.app>
