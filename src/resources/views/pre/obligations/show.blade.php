@php
    use App\Support\Format;
    use App\Support\Reference;

    $refLabel = $document->referenceLabel();
    $total = $document->total();
    $allotmentBalance = (float) $document->allotment_balance;
    $remainingBalance = $document->remainingBalance();
@endphp

<x-layouts.app>
    <div class="space-y-6 text-[13px]">
        <x-page-title :title="$cfg['noun'].' '.$document->serial"
                      :description="$cfg['description']">
            <x-slot:action>
                <x-button variant="secondary" :href="route($cfg['routePrefix'].'.index')">Back to {{ $cfg['title'] }}</x-button>
            </x-slot:action>
        </x-page-title>

        <x-card class="p-5 space-y-6">
            {{-- ---- Header summary ---- --}}
            <section class="grid sm:grid-cols-2 gap-x-6 gap-y-2">
                <x-obligations.meta label="Serial No.">
                    <span class="font-mono">{{ $document->serial }}</span>
                </x-obligations.meta>
                <x-obligations.meta label="Status">
                    <x-status-badge :status="$document->status" />
                </x-obligations.meta>
                <x-obligations.meta label="Date">{{ Format::shortDate($document->date) }}</x-obligations.meta>
                <x-obligations.meta label="Fund Cluster">
                    {{ Reference::fundClusterLabel($document->fund_cluster) }}
                </x-obligations.meta>
                <x-obligations.meta label="Payee">{{ $document->payee_name ?: '—' }}</x-obligations.meta>
                <x-obligations.meta label="Office / Address">
                    {{ collect([$document->office, $document->address])->filter()->join(' · ') ?: '—' }}
                </x-obligations.meta>
                @if ($allotmentBalance > 0)
                    <x-obligations.meta label="Allotment Balance">
                        {{ Format::peso($allotmentBalance) }}
                    </x-obligations.meta>
                    <x-obligations.meta label="Remaining Balance" :class="$remainingBalance < 0 ? 'text-danger' : ''">
                        {{ Format::peso($remainingBalance) }}
                    </x-obligations.meta>
                @endif
            </section>

            {{-- ---- Line items ---- --}}
            <section>
                <div class="font-semibold text-ink mb-2">Particulars</div>
                <div class="border border-line rounded-lg overflow-hidden">
                    <x-table>
                        <thead>
                            <tr>
                                <x-th>Responsibility Center</x-th>
                                <x-th>MFO/PAP</x-th>
                                <x-th>UACS · Particulars</x-th>
                                <x-th right>Amount</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($document->lines as $line)
                                @php $mfo = $line->mfoPap(); @endphp
                                <tr>
                                    <x-td>
                                        <div class="font-medium text-ink">{{ $line->rc_acronym }}</div>
                                        <div class="text-[12px] text-ink-mute font-mono">{{ $line->rc_code }}</div>
                                    </x-td>
                                    <x-td>
                                        @if ($mfo)
                                            <span class="text-[12px]">
                                                <span class="font-mono">{{ $mfo['code'] }}</span><br>{{ $mfo['name'] }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </x-td>
                                    <x-td>
                                        <div class="font-mono text-[12px] text-ink-soft">{{ $line->object_code }}</div>
                                        <div>{{ $line->particulars }}</div>
                                    </x-td>
                                    <x-td right>{{ Format::peso($line->amount) }}</x-td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-canvas font-semibold">
                                <x-td>TOTAL</x-td>
                                <x-td />
                                <x-td />
                                <x-td right>{{ Format::peso($total) }}</x-td>
                            </tr>
                        </tfoot>
                    </x-table>
                </div>
            </section>

            {{-- ---- Certifications ---- --}}
            <section class="grid sm:grid-cols-2 gap-4">
                <x-obligations.cert-view title="Certification A"
                                         :officer-id="$document->cert_a_officer_id"
                                         :date="$document->cert_a_date" />
                <x-obligations.cert-view title="Certification B"
                                         :officer-id="$document->cert_b_officer_id"
                                         :date="$document->cert_b_date" />
            </section>

            {{-- ---- Status of Obligation / Utilization ledger ---- --}}
            <section>
                <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                    <span class="font-semibold text-ink">Status of {{ $refLabel }}</span>
                    <div class="flex items-center gap-3 text-[12px] text-ink-mute">
                        <span>Not Yet Due: <span class="text-ink-soft tnum">{{ Format::amt($document->notYetDue()) }}</span></span>
                        <span>Due &amp; Demandable: <span class="text-ink-soft tnum">{{ Format::amt($document->dueDemandable()) }}</span></span>
                    </div>
                </div>

                <div class="border border-line rounded-lg overflow-hidden">
                    <x-table>
                        <thead>
                            <tr>
                                <x-th>Date</x-th>
                                <x-th>Reference No.</x-th>
                                <x-th right>{{ $refLabel }} (a)</x-th>
                                <x-th right>Payable (b)</x-th>
                                <x-th right>Payment (c)</x-th>
                                <x-th right>Not Yet Due (a−b)</x-th>
                                <x-th right>Due &amp; Demandable (b−c)</x-th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ledgerRows as $row)
                                @php $entry = $row['entry']; @endphp
                                <tr>
                                    <x-td>{{ Format::longDate($entry->entry_date) }}</x-td>
                                    <x-td>
                                        <span class="font-mono text-[12px]">{{ $entry->reference_no }}</span>
                                        <x-badge tone="neutral">{{ $entry->kind }}</x-badge>
                                    </x-td>
                                    <x-td right>{{ $entry->kind === 'obligation' ? Format::amt($entry->amount) : '—' }}</x-td>
                                    <x-td right>{{ $entry->kind === 'payable' ? Format::amt($entry->amount) : '—' }}</x-td>
                                    <x-td right>{{ $entry->kind === 'payment' ? Format::amt($entry->amount) : '—' }}</x-td>
                                    <x-td right>{{ Format::amt($row['notYetDue']) }}</x-td>
                                    <x-td right>{{ Format::amt($row['dueDemandable']) }}</x-td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-table>
                </div>

                {{-- ---- Add entry ---- --}}
                <form method="POST" action="{{ route('pre.ledger.store', $document) }}"
                      class="mt-3 border border-line rounded-lg p-4 grid gap-3 sm:grid-cols-5 items-end">
                    @csrf

                    <x-field label="Entry type">
                        <x-select name="kind" data-ledger-kind>
                            <option value="payable" @selected(old('kind') === 'payable')>Payable (obligated / DV)</option>
                            <option value="payment" @selected(old('kind') === 'payment')>Payment</option>
                        </x-select>
                    </x-field>

                    <x-field label="Date">
                        <x-input type="date" name="entry_date" value="{{ old('entry_date', now()->toDateString()) }}" />
                    </x-field>

                    <div data-when-payment hidden>
                        <x-field label="Instrument">
                            <x-select name="instrument">
                                @foreach ($instruments as $instrument)
                                    <option value="{{ $instrument }}" @selected(old('instrument') === $instrument)>{{ $instrument }}</option>
                                @endforeach
                            </x-select>
                        </x-field>
                    </div>

                    <div data-when-payable>
                        <x-field label="Disbursement Voucher">
                            <x-input name="reference_no" value="{{ old('reference_no') }}" placeholder="DV no." />
                        </x-field>
                    </div>

                    <div data-when-payment hidden>
                        <x-field label="Instrument No.">
                            <x-input name="reference_no" value="{{ old('reference_no') }}" placeholder="No." />
                        </x-field>
                    </div>

                    <x-field label="Amount" :error="$errors->first('amount')">
                        <x-input name="amount" inputmode="decimal" value="{{ old('amount') }}" placeholder="0.00"
                                 class="text-right tnum" />
                    </x-field>

                    <x-button type="submit" class="sm:col-start-5">
                        <x-icon name="plus" class="size-4" /> Add entry
                    </x-button>
                </form>

                <p class="text-[12px] text-ink-mute mt-2">
                    Payable references a Disbursement Voucher; Payment references a
                    {{ implode(' / ', $instruments) }} instrument number. Balances are computed automatically — never
                    typed. Total {{ strtolower($refLabel) }}: <span class="tnum">{{ Format::peso($total) }}</span>.
                </p>
            </section>
        </x-card>
    </div>
</x-layouts.app>
