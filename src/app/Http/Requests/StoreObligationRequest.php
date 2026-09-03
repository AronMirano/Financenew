<?php

namespace App\Http\Requests;

use App\Support\Reference;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Validates a new OBR / BUR. Mirrors the prototype's `isValid` rule:
 * a fund cluster, a payee, and at least one complete line with a
 * positive amount.
 */
class StoreObligationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rcAcronyms = array_column(Reference::RC_RECORDS, 'acronym');
        $objectCodes = array_column(Reference::UACS_RECORDS, 'code');
        $clusters = array_column(Reference::FUND_CLUSTERS, 'code');
        $officers = array_column(Reference::PERSONNEL, 'id');

        return [
            'date' => ['required', 'date'],
            'fund_cluster' => ['required', 'string', 'in:'.implode(',', $clusters)],
            'payee_name' => ['required', 'string', 'max:255'],
            'office' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],

            'lines' => ['required', 'array', 'min:1'],
            'lines.*.rc_acronym' => ['nullable', 'string', 'in:'.implode(',', $rcAcronyms)],
            'lines.*.object_code' => ['nullable', 'string', 'in:'.implode(',', $objectCodes)],
            'lines.*.particulars' => ['nullable', 'string', 'max:255'],
            'lines.*.amount' => ['nullable', 'numeric', 'min:0'],

            'cert_a_officer_id' => ['nullable', 'string', 'in:'.implode(',', $officers)],
            'cert_b_officer_id' => ['nullable', 'string', 'in:'.implode(',', $officers)],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'lines.required' => 'Add at least one Particulars line.',
            'fund_cluster.in' => 'Select a valid fund cluster.',
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->completeLines() === []) {
                $validator->errors()->add(
                    'lines',
                    'At least one line needs a responsibility center, a UACS object code, and an amount greater than zero.',
                );
            }
        });
    }

    /**
     * The lines that are complete enough to persist — the same filter the
     * prototype applied in `build()`, plus the positive-amount rule.
     *
     * @return list<array{rc_acronym: string, rc_code: string, object_code: string, particulars: string, amount: float}>
     */
    public function completeLines(): array
    {
        $lines = [];

        foreach ((array) $this->input('lines', []) as $line) {
            $rcAcronym = trim((string) ($line['rc_acronym'] ?? ''));
            $objectCode = trim((string) ($line['object_code'] ?? ''));
            $amount = (float) ($line['amount'] ?? 0);

            if ($rcAcronym === '' || $objectCode === '' || $amount <= 0) {
                continue;
            }

            $rc = Reference::lookupRcByAcronym($rcAcronym);
            $particulars = trim((string) ($line['particulars'] ?? ''));

            $lines[] = [
                'rc_acronym' => $rcAcronym,
                'rc_code' => $rc['code'] ?? '',
                'object_code' => $objectCode,
                'particulars' => $particulars !== '' ? $particulars : Reference::lookupUacsTitle($objectCode),
                'amount' => $amount,
            ];
        }

        return $lines;
    }
}
