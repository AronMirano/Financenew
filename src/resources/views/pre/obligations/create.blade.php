@php
    use App\Support\Format;
    use App\Support\Reference;

    $refLabel = $cfg['kind'] === 'obligation' ? 'Obligation' : 'Utilization';
    $lines = old('lines', [['rc_acronym' => '', 'object_code' => '', 'particulars' => '', 'amount' => '']]);
    $total = Format::sum(array_map(fn ($l) => (float) ($l['amount'] ?? 0), $lines));
@endphp

<x-layouts.app>
    <form method="POST" action="{{ route($cfg['routePrefix'].'.store') }}" class="space-y-6 text-[13px]">
        @csrf

        <x-page-title :title="$cfg['fullName']"
                      description="Complete the header, the Particulars lines and the certifications. Balances are computed automatically — never typed.">
            <x-slot:action>
                <x-button variant="secondary" :href="route($cfg['routePrefix'].'.index')">Cancel</x-button>
            </x-slot:action>
        </x-page-title>

        @if ($errors->any())
            <div class="border border-danger rounded-lg px-4 py-3 text-[13px] text-danger" role="alert">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-card class="p-5 space-y-6">
            {{-- ---- Header ---- --}}
            <section class="grid sm:grid-cols-2 gap-4">
                <x-field label="Serial No." hint="System-generated on save">
                    <x-input value="{{ $serialPreview }}" disabled />
                </x-field>

                <x-field label="Date" required :error="$errors->first('date')">
                    <x-input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required />
                </x-field>

                <x-field label="Fund Cluster" required :error="$errors->first('fund_cluster')">
                    <x-select name="fund_cluster" required>
                        <option value="">Select fund cluster…</option>
                        @foreach (Reference::FUND_CLUSTERS as $f)
                            <option value="{{ $f['code'] }}"
                                    @selected(old('fund_cluster', $defaultFundCluster) === $f['code'])>
                                {{ $f['label'] }}
                            </option>
                        @endforeach
                    </x-select>
                </x-field>

                <x-field label="Payee Name" required :error="$errors->first('payee_name')">
                    <x-input name="payee_name" value="{{ old('payee_name') }}" list="payees-{{ $cfg['kind'] }}"
                             placeholder="Enter or select payee" required />
                    <datalist id="payees-{{ $cfg['kind'] }}">
                        @foreach ($knownPayees as $payee)
                            <option value="{{ $payee }}"></option>
                        @endforeach
                    </datalist>
                </x-field>

                <x-field label="Office">
                    <x-input name="office" value="{{ old('office') }}" placeholder="Enter office" />
                </x-field>

                <x-field label="Address" hint="Fixed address for MMSU">
                    <x-input name="address" value="{{ $fixedAddress }}" readonly />
                    <input type="hidden" name="address" value="{{ $fixedAddress }}">
                </x-field>

                <x-field label="Allotment Balance" hint="Total allotment available for this office (enter manually)">
                    <x-input name="allotment_balance" type="number" step="0.01" min="0"
                             value="{{ old('allotment_balance', 0) }}" inputmode="decimal"
                             placeholder="0.00" class="text-right tnum" />
                </x-field>
            </section>

            {{-- ---- Line items ---- --}}
            <section>
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-ink">Particulars — RC · UACS · Amount</span>
                    <x-button size="sm" variant="secondary" data-add-line>
                        <x-icon name="plus" class="size-4" /> Add line
                    </x-button>
                </div>

                <div class="border border-line rounded-lg overflow-hidden">
                    <x-table>
                        <thead>
                            <tr>
                                <x-th class="min-w-[220px]">Responsibility Center</x-th>
                                <x-th class="min-w-[260px]">UACS Object Code / Particulars</x-th>
                                <x-th>Class</x-th>
                                <x-th right>Amount</x-th>
                                <x-th />
                            </tr>
                        </thead>
                        <tbody data-line-body>
                            @foreach ($lines as $i => $line)
                                @php
                                    $rc = Reference::lookupRcByAcronym($line['rc_acronym'] ?? '');
                                    $mfo = $rc ? Reference::mfoPapForCategory($rc['category']) : null;
                                    $cls = Reference::uacsClass($line['object_code'] ?? '');
                                @endphp
                                <tr data-line-row class="align-top">
                                    <x-td>
                                        <x-select name="lines[{{ $i }}][rc_acronym]">
                                            <option value="">Select RC (acronym)…</option>
                                            @foreach (Reference::RC_RECORDS as $r)
                                                <option value="{{ $r['acronym'] }}"
                                                        @selected(($line['rc_acronym'] ?? '') === $r['acronym'])>
                                                    {{ $r['acronym'] }} — {{ $r['name'] }}
                                                </option>
                                            @endforeach
                                        </x-select>
                                        @if ($rc)
                                            <div class="mt-1.5 text-[12px] text-ink-mute leading-relaxed">
                                                <div>RC Code: <span class="font-mono text-ink-soft">{{ $rc['code'] }}</span></div>
                                                @if ($mfo)
                                                    <div>MFO/PAP: <span class="font-mono text-ink-soft">{{ $mfo['code'] }}</span> · {{ $mfo['name'] }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </x-td>

                                    <x-td>
                                        <x-select name="lines[{{ $i }}][object_code]" data-object-code>
                                            <option value="">Select UACS object code…</option>
                                            @foreach (Reference::UACS_RECORDS as $u)
                                                <option value="{{ $u['code'] }}" data-cls="{{ $u['cls'] }}"
                                                        data-title="{{ $u['title'] }}"
                                                        @selected(($line['object_code'] ?? '') === $u['code'])>
                                                    {{ $u['code'] }} — {{ $u['title'] }}
                                                </option>
                                            @endforeach
                                        </x-select>
                                        <x-input name="lines[{{ $i }}][particulars]" data-particulars class="mt-1.5"
                                                 value="{{ $line['particulars'] ?? '' }}"
                                                 placeholder="Particulars (auto-filled from object code)" />
                                    </x-td>

                                    <x-td>
                                        <span data-class-badge class="{{ $cls ? '' : 'text-ink-mute' }}">{{ $cls ?? '—' }}</span>
                                    </x-td>

                                    <x-td right>
                                        <x-input name="lines[{{ $i }}][amount]" inputmode="decimal"
                                                 value="{{ $line['amount'] ?? '' }}" placeholder="0.00"
                                                 class="text-right tnum w-32 ml-auto" />
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
                                <x-td>TOTAL AMOUNT</x-td>
                                <x-td />
                                <x-td />
                                <x-td right>{{ Format::peso($total) }}</x-td>
                                <x-td />
                            </tr>
                        </tfoot>
                    </x-table>
                </div>
            </section>

            {{-- ---- Certifications ---- --}}
            <section class="grid sm:grid-cols-2 gap-4">
                <x-obligations.cert-field
                    title="Certification A"
                    note="Charges are lawful, necessary and supported by valid documents."
                    label="Certifying / Requesting Officer"
                    name="cert_a_officer_id"
                    :selected="old('cert_a_officer_id')" />

                <x-obligations.cert-field
                    title="Certification B"
                    :note="$cfg['certBNote']"
                    label="Budget Officer"
                    name="cert_b_officer_id"
                    :selected="old('cert_b_officer_id', $defaultCertB)" />
            </section>

            <p class="text-[12px] text-ink-mute">
                On save the {{ $refLabel }} Request is created with a system serial number and the Status of
                {{ $refLabel }} ledger is seeded with the total obligation. Balances are computed automatically as
                payable and payment entries are added.
            </p>

            <div class="flex items-center justify-end gap-2 border-t border-line pt-4">
                <x-button variant="secondary" :href="route($cfg['routePrefix'].'.index')">Cancel</x-button>
                <x-button type="submit">Save {{ $cfg['noun'] }}</x-button>
            </div>
        </x-card>
    </form>

    {{-- Template for JS-added Particulars rows. --}}
    <template data-line-template>
        <tr data-line-row class="align-top">
            <td class="px-3 py-2.5 border-b border-line-soft text-ink align-middle">
                <div class="relative">
                    <select name="lines[__INDEX__][rc_acronym]"
                            class="w-full h-9 pl-3 pr-9 rounded-lg border border-line bg-surface text-sm text-ink appearance-none focus-ring focus:border-brand-500 transition-colors">
                        <option value="">Select RC (acronym)…</option>
                        @foreach (Reference::RC_RECORDS as $r)
                            <option value="{{ $r['acronym'] }}">{{ $r['acronym'] }} — {{ $r['name'] }}</option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="size-4 text-ink-mute absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                </div>
            </td>
            <td class="px-3 py-2.5 border-b border-line-soft text-ink align-middle">
                <div class="relative">
                    <select name="lines[__INDEX__][object_code]" data-object-code
                            class="w-full h-9 pl-3 pr-9 rounded-lg border border-line bg-surface text-sm text-ink appearance-none focus-ring focus:border-brand-500 transition-colors">
                        <option value="">Select UACS object code…</option>
                        @foreach (Reference::UACS_RECORDS as $u)
                            <option value="{{ $u['code'] }}" data-cls="{{ $u['cls'] }}" data-title="{{ $u['title'] }}">
                                {{ $u['code'] }} — {{ $u['title'] }}
                            </option>
                        @endforeach
                    </select>
                    <x-icon name="chevron-down" class="size-4 text-ink-mute absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                </div>
                <input name="lines[__INDEX__][particulars]" data-particulars
                       placeholder="Particulars (auto-filled from object code)"
                       class="mt-1.5 w-full h-9 px-3 rounded-lg border border-line bg-surface text-sm text-ink placeholder:text-ink-mute focus-ring focus:border-brand-500 transition-colors">
            </td>
            <td class="px-3 py-2.5 border-b border-line-soft text-ink align-middle">
                <span data-class-badge class="text-ink-mute">—</span>
            </td>
            <td class="px-3 py-2.5 border-b border-line-soft text-ink align-middle text-right tnum">
                <input name="lines[__INDEX__][amount]" inputmode="decimal" placeholder="0.00"
                       class="w-full h-9 px-3 rounded-lg border border-line bg-surface text-sm text-ink placeholder:text-ink-mute focus-ring focus:border-brand-500 transition-colors text-right tnum w-32 ml-auto">
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
