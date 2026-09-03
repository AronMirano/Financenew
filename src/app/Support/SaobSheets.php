<?php

namespace App\Support;

/**
 * SAOB per-sheet reference categories.
 *
 * Sheet/category terminology preserved verbatim from the SAOB workbooks.
 * These are reference terminology (sheet/category codes), not transactional
 * records — the figures themselves live in the `saob_lines` table and remain
 * empty until data is entered or imported.
 */
class SaobSheets
{
    /** @var list<array{key: string, code: string, name: string, mooe_only: bool}> */
    public const SHEETS = [
        ['key' => '100', 'code' => '100', 'name' => 'General Administration & Support', 'mooe_only' => false],
        ['key' => '200', 'code' => '200', 'name' => 'Support to Operations / Auxiliary', 'mooe_only' => false],
        ['key' => '301', 'code' => '301', 'name' => 'Higher Education Program', 'mooe_only' => false],
        ['key' => 'FHE', 'code' => 'FHE', 'name' => 'Free Higher Education', 'mooe_only' => true],
        ['key' => '302', 'code' => '302', 'name' => 'Advanced Education Program', 'mooe_only' => false],
        ['key' => '303', 'code' => '303', 'name' => 'Research Program', 'mooe_only' => false],
        ['key' => '303NB', 'code' => '303-NBERIC', 'name' => 'Research Program / NBERIC', 'mooe_only' => true],
        ['key' => 'MFO4', 'code' => '304', 'name' => 'Technical Advisory Extension Program', 'mooe_only' => false],
    ];

    /** The 11-column SAOB model, in workbook column order. */
    public const COLUMNS = [
        ['key' => 'authorizedAppropriations', 'label' => 'Authorized Appropriations', 'n' => 1],
        ['key' => 'allotmentReceived', 'label' => 'Allotment Received', 'n' => 2],
        ['key' => 'augmentations', 'label' => 'Augmentations', 'n' => 3],
        ['key' => 'modifications', 'label' => 'Modifications', 'n' => 4],
        ['key' => 'adjustedAppropriations', 'label' => 'Adjusted Appropriations', 'n' => 5],
        ['key' => 'adjustedAllotment', 'label' => 'Adjusted Allotment', 'n' => 6],
        ['key' => 'unreleasedAppropriations', 'label' => 'Unreleased Approp.', 'n' => 7],
        ['key' => 'obligationsIncurred', 'label' => 'Obligations Incurred', 'n' => 8],
        ['key' => 'unobligatedAllotment', 'label' => 'Unobligated Allotment', 'n' => 9],
        ['key' => 'unpaidObligations', 'label' => 'Unpaid Obligations', 'n' => 10],
        ['key' => 'disbursements', 'label' => 'Disbursements', 'n' => 11],
    ];

    /** @return array{key: string, code: string, name: string, mooe_only: bool} */
    public static function find(?string $key): array
    {
        foreach (self::SHEETS as $sheet) {
            if ($sheet['key'] === $key) {
                return $sheet;
            }
        }

        return self::SHEETS[0];
    }
}
