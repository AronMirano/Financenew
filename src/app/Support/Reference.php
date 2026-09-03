<?php

namespace App\Support;

/**
 * Reference data — transcribed from the source Excel workbooks
 * (OBRS.xlsx / BURS.xlsx `RC` and `UACS Code` sheets, SAOB sheets).
 * These back the VLOOKUP relationships used across the system:
 *   RC Name    = VLOOKUP(RC Code, RC!A:B, 2, FALSE)
 *   Acct Title = VLOOKUP(Object Code, UACS!A:B, 2, FALSE)
 */
class Reference
{
    /** Fund categories keyed by UACS program segment. */
    public const FUND_CATEGORIES = [
        '100' => 'General Administration & Support',
        '200' => 'Support to Operations',
        '301' => 'Higher Education Program',
        '302' => 'Advanced Education Program',
        '303' => 'Research Program',
        '304' => 'Technical Advisory Extension Program',
    ];

    /** Fund clusters seen in the OBR / BUR forms. */
    public const FUND_CLUSTERS = [
        ['code' => '01-1-01-101', 'label' => '01-1-01-101 — Regular Agency Fund'],
        ['code' => '05206441', 'label' => '05206441 — Internally Generated / Trust (IGP)'],
    ];

    public const EXPENSE_CLASSES = [
        'PS' => 'Personnel Services',
        'MOOE' => 'Maintenance & Other Operating Expenses',
        'CO' => 'Capital Outlays',
    ];

    /** A representative subset of the ~400-row RC sheet (verbatim). */
    public const RC_RECORDS = [
        ['acronym' => 'OP', 'code' => '01-01', 'category' => '100', 'name' => 'Office of the President'],
        ['acronym' => 'LEGAL', 'code' => '01-01-04', 'category' => '100', 'name' => 'Legal Affairs'],
        ['acronym' => 'ITC', 'code' => '01-01-06', 'category' => '301', 'name' => 'Information Technology Center'],
        ['acronym' => 'BAC', 'code' => '01-01-10', 'category' => '100', 'name' => 'Procurement Service Unit'],
        ['acronym' => 'OVPAF', 'code' => '01-01-12', 'category' => '100', 'name' => 'Office of the Vice President for Administration and Finance'],
        ['acronym' => 'SUPPLY', 'code' => '01-01-12-01-01', 'category' => '100', 'name' => 'Supply & Property Management'],
        ['acronym' => 'HR', 'code' => '01-01-12-02', 'category' => '100', 'name' => 'Human Resources Management'],
        ['acronym' => 'Clinic', 'code' => '01-01-12-03', 'category' => '200', 'name' => 'Health and Wellness Services'],
        ['acronym' => 'PPGSD', 'code' => '01-01-12-04', 'category' => '100', 'name' => 'Physical Plant and General Services Division'],
        ['acronym' => 'OVPAA', 'code' => '01-01-13', 'category' => '301', 'name' => 'Office of the Vice President for Academic Affairs'],
        ['acronym' => 'CAFSD', 'code' => '01-01-13-01', 'category' => '301', 'name' => 'College of Agriculture, Food and Sustainable Development'],
        ['acronym' => 'CAS', 'code' => '01-01-13-02', 'category' => '301', 'name' => 'College of Arts and Sciences'],
        ['acronym' => 'CASAT', 'code' => '01-01-13-03', 'category' => '301', 'name' => 'College of Aquatic Sciences & Applied Technology'],
        ['acronym' => 'CBEA', 'code' => '01-01-13-04', 'category' => '301', 'name' => 'College of Business, Economics and Accountancy'],
        ['acronym' => 'CHS', 'code' => '01-01-13-05', 'category' => '301', 'name' => 'College of Health Sciences'],
        ['acronym' => 'CIT', 'code' => '01-01-13-06', 'category' => '301', 'name' => 'College of Industrial Technology'],
        ['acronym' => 'COE', 'code' => '01-01-13-07', 'category' => '301', 'name' => 'College of Engineering'],
        ['acronym' => 'CTE', 'code' => '01-01-13-08', 'category' => '301', 'name' => 'College of Teacher Education'],
        ['acronym' => 'GS', 'code' => '01-01-13-09', 'category' => '302', 'name' => 'Graduate School'],
        ['acronym' => 'CL', 'code' => '01-01-13-10', 'category' => '301', 'name' => 'College of Law'],
        ['acronym' => 'COM', 'code' => '01-01-13-11', 'category' => '301', 'name' => 'College of Medicine'],
        ['acronym' => 'ULS', 'code' => '01-01-13-17', 'category' => '200', 'name' => 'University Library System'],
        ['acronym' => 'OVPREB', 'code' => '01-01-14', 'category' => '303', 'name' => 'Office of the Vice President for Research, Extension and Business'],
        ['acronym' => 'RD', 'code' => '01-01-14-01', 'category' => '303', 'name' => 'Research Directorate'],
        ['acronym' => 'GRC', 'code' => '01-01-14-01-01-05', 'category' => '303', 'name' => 'Garlic Research Center'],
        ['acronym' => 'NBERIC', 'code' => '01-01-14-01-01-11', 'category' => '303', 'name' => 'National Bioenergy Research and Innovation Center'],
        ['acronym' => 'ED', 'code' => '01-01-14-02', 'category' => '304', 'name' => 'Extension Directorate'],
        ['acronym' => 'BD', 'code' => '01-01-14-03', 'category' => '200', 'name' => 'Business Directorate'],
        ['acronym' => 'MP', 'code' => '01-01-14-03-04', 'category' => '200', 'name' => 'Meat Processing'],
        ['acronym' => 'FINANCE', 'code' => '01-01-15-02', 'category' => '100', 'name' => 'Finance Directorate'],
        ['acronym' => 'BUDGET', 'code' => '01-01-15-02-02', 'category' => '100', 'name' => 'Budget Section'],
        ['acronym' => 'ACCOUNTING', 'code' => '01-01-15-02-03', 'category' => '100', 'name' => 'Accounting Section'],
        ['acronym' => 'MMSU IGP', 'code' => '01-01-13-07', 'category' => '301', 'name' => 'MMSU Income Generating Project'],
    ];

    /** Representative subset of the `UACS Code` sheet (verbatim codes & titles). */
    public const UACS_RECORDS = [
        // Personnel Services (501…)
        ['code' => '5010101001', 'title' => 'Basic Salary - Civilian', 'cls' => 'PS'],
        ['code' => '5010102000', 'title' => 'Salaries and Wages - Casual/Contractual', 'cls' => 'PS'],
        ['code' => '5010201001', 'title' => 'PERA - Civilian', 'cls' => 'PS'],
        ['code' => '5010202000', 'title' => 'Representation Allowance (RA)', 'cls' => 'PS'],
        ['code' => '5010203001', 'title' => 'Transportation Allowance (TA)', 'cls' => 'PS'],
        ['code' => '5010204001', 'title' => 'Clothing/Uniform Allowance - Civilian', 'cls' => 'PS'],
        ['code' => '5010210001', 'title' => 'Honoraria - Civilian', 'cls' => 'PS'],
        ['code' => '5010214001', 'title' => 'Year End Bonus - Civilian', 'cls' => 'PS'],
        ['code' => '5010215001', 'title' => 'Cash Gift - Civilian', 'cls' => 'PS'],
        ['code' => '5010216001', 'title' => 'Mid-Year Bonus - Civilian', 'cls' => 'PS'],
        ['code' => '5010301000', 'title' => 'Retirement and Life Insurance Premiums', 'cls' => 'PS'],
        ['code' => '5010302001', 'title' => 'Pag-IBIG - Civilian', 'cls' => 'PS'],
        ['code' => '5010303001', 'title' => 'PhilHealth - Civilian', 'cls' => 'PS'],
        ['code' => '5010304001', 'title' => 'ECIP - Civilian', 'cls' => 'PS'],
        ['code' => '5010403001-01', 'title' => 'Terminal Leave Benefits - Civilian', 'cls' => 'PS'],
        // MOOE (502…)
        ['code' => '5020101000', 'title' => 'Traveling Expenses - Local', 'cls' => 'MOOE'],
        ['code' => '5020201002', 'title' => 'Training Expenses', 'cls' => 'MOOE'],
        ['code' => '5020202000', 'title' => 'Scholarship Grants/Expenses', 'cls' => 'MOOE'],
        ['code' => '5020301002', 'title' => 'Office Supplies Expenses', 'cls' => 'MOOE'],
        ['code' => '5020307000', 'title' => 'Drugs and Medicines Expenses', 'cls' => 'MOOE'],
        ['code' => '5020309000', 'title' => 'Fuel, Oil and Lubricants Expenses', 'cls' => 'MOOE'],
        ['code' => '5020310000', 'title' => 'Agricultural and Marine Supplies Expenses', 'cls' => 'MOOE'],
        ['code' => '5020321003', 'title' => 'Semi-Expendable - ICT Equipment', 'cls' => 'MOOE'],
        ['code' => '5020399000', 'title' => 'Other Supplies and Materials Expenses', 'cls' => 'MOOE'],
        ['code' => '5020401000', 'title' => 'Water Expenses', 'cls' => 'MOOE'],
        ['code' => '5020402000', 'title' => 'Electricity Expenses', 'cls' => 'MOOE'],
        ['code' => '5020502001', 'title' => 'Telephone Expenses (Mobile)', 'cls' => 'MOOE'],
        ['code' => '5020503000', 'title' => 'Internet Subscription Expenses', 'cls' => 'MOOE'],
        ['code' => '5021199000', 'title' => 'Other Professional Services', 'cls' => 'MOOE'],
        ['code' => '5021202000', 'title' => 'Janitorial Services', 'cls' => 'MOOE'],
        ['code' => '5021203000', 'title' => 'Security Services', 'cls' => 'MOOE'],
        ['code' => '5021299000', 'title' => 'Other General Services', 'cls' => 'MOOE'],
        ['code' => '5021304001', 'title' => 'Repairs and Maintenance - Buildings', 'cls' => 'MOOE'],
        ['code' => '5021305003', 'title' => 'Repairs and Maintenance - ICT Equipment', 'cls' => 'MOOE'],
        ['code' => '5021306001', 'title' => 'Repairs and Maintenance - Motor Vehicles', 'cls' => 'MOOE'],
        ['code' => '5021499000', 'title' => 'Subsidies - Others', 'cls' => 'MOOE'],
        ['code' => '5021501001', 'title' => 'Taxes, Duties and Licenses', 'cls' => 'MOOE'],
        ['code' => '5021503000', 'title' => 'Insurance Expenses', 'cls' => 'MOOE'],
        ['code' => '5021601000', 'title' => 'Labor and Wages', 'cls' => 'MOOE'],
        ['code' => '5029902000', 'title' => 'Printing and Publication Expenses', 'cls' => 'MOOE'],
        ['code' => '5029903000', 'title' => 'Representation Expenses', 'cls' => 'MOOE'],
        ['code' => '5029905004', 'title' => 'Rents - Equipment', 'cls' => 'MOOE'],
        ['code' => '5029907001', 'title' => 'ICT Software Subscription', 'cls' => 'MOOE'],
        ['code' => '5029999099', 'title' => 'Other Maintenance and Operating Expenses', 'cls' => 'MOOE'],
        // Capital Outlays (506…)
        ['code' => '5060405002', 'title' => 'Office Equipment', 'cls' => 'CO'],
        ['code' => '5060405003', 'title' => 'Information and Communication Technology Equipment', 'cls' => 'CO'],
        ['code' => '5060405099', 'title' => 'Other Machinery and Equipment', 'cls' => 'CO'],
        ['code' => '5060404001', 'title' => 'Buildings', 'cls' => 'CO'],
        ['code' => '5060404002', 'title' => 'School Buildings', 'cls' => 'CO'],
        ['code' => '5060407001', 'title' => 'Furniture and Fixtures', 'cls' => 'CO'],
    ];

    /**
     * MFO / PAP — Major Final Output / Program-Activity-Project.
     * Editable placeholder set: one representative MFO/PAP per program
     * category. Correct the codes/names later as needed.
     */
    public const MFO_PAP_RECORDS = [
        ['code' => '100000100001000', 'name' => 'General Administration and Support Services', 'category' => '100'],
        ['code' => '200000100001000', 'name' => 'Support to Operations', 'category' => '200'],
        ['code' => '310100100001000', 'name' => 'Higher Education Services', 'category' => '301'],
        ['code' => '320100100001000', 'name' => 'Advanced Education Services', 'category' => '302'],
        ['code' => '320200100002000', 'name' => 'Research Services', 'category' => '303'],
        ['code' => '320300100003000', 'name' => 'Technical Advisory Extension Services', 'category' => '304'],
    ];

    /**
     * Personnel — editable placeholder roster for certifying and
     * budget officers on the OBR / BUR forms. Correct values later.
     */
    public const PERSONNEL = [
        ['id' => 'p-austria', 'name' => 'Kevin John D. Austria', 'position' => 'Administrative Officer IV · Budget Officer II'],
        ['id' => 'p-agpaoa', 'name' => 'Ma. Theresa R. Agpaoa', 'position' => 'Accountant III'],
        ['id' => 'p-bumanglag', 'name' => 'Ramon P. Bumanglag', 'position' => 'Chief Administrative Officer'],
        ['id' => 'p-corpuz', 'name' => 'Elena M. Corpuz', 'position' => 'Budget Officer III'],
        ['id' => 'p-dela-cruz', 'name' => 'Rogelio S. Dela Cruz', 'position' => 'Vice President for Administration and Finance'],
        ['id' => 'p-ramos', 'name' => 'Josefina T. Ramos', 'position' => 'Head of Office / Requesting Officer'],
    ];

    /** Payment instruments differ by document type. */
    public const PAYMENT_INSTRUMENTS = [
        'obligation' => ['Check', 'ADA', 'TRA'],
        'utilization' => ['RCI', 'RADAI', 'RTRAI'],
    ];

    // ---- Lookup helpers (mirror the workbook VLOOKUPs) -------------------

    public static function lookupRcName(string $code): string
    {
        foreach (self::RC_RECORDS as $r) {
            if ($r['code'] === $code) {
                return $r['name'];
            }
        }

        return '#N/A';
    }

    /** @return array{acronym: string, code: string, category: string, name: string}|null */
    public static function lookupRcByAcronym(?string $acronym): ?array
    {
        foreach (self::RC_RECORDS as $r) {
            if ($r['acronym'] === $acronym) {
                return $r;
            }
        }

        return null;
    }

    public static function lookupUacsTitle(?string $code): string
    {
        foreach (self::UACS_RECORDS as $u) {
            if ($u['code'] === $code) {
                return $u['title'];
            }
        }

        return '#N/A';
    }

    public static function uacsClass(?string $code): ?string
    {
        foreach (self::UACS_RECORDS as $u) {
            if ($u['code'] === $code) {
                return $u['cls'];
            }
        }

        return null;
    }

    /** @return array{code: string, name: string, category: string}|null */
    public static function mfoPapForCategory(?string $category): ?array
    {
        foreach (self::MFO_PAP_RECORDS as $m) {
            if ($m['category'] === $category) {
                return $m;
            }
        }

        return null;
    }

    /** @return array{code: string, name: string, category: string}|null */
    public static function mfoPapForRcAcronym(?string $acronym): ?array
    {
        $rc = self::lookupRcByAcronym($acronym);

        return $rc ? self::mfoPapForCategory($rc['category']) : null;
    }

    /** @return array{id: string, name: string, position: string}|null */
    public static function lookupPersonnel(?string $id): ?array
    {
        foreach (self::PERSONNEL as $p) {
            if ($p['id'] === $id) {
                return $p;
            }
        }

        return null;
    }

    public static function fundClusterLabel(?string $code): string
    {
        foreach (self::FUND_CLUSTERS as $f) {
            if ($f['code'] === $code) {
                return $f['label'];
            }
        }

        return (string) $code;
    }

    /** Standard default fund cluster per document type (editable in the form). */
    public static function defaultFundCluster(string $kind): string
    {
        return $kind === 'utilization' ? '05206441' : '01-1-01-101';
    }

    /** @return list<string> */
    public static function paymentInstruments(string $kind): array
    {
        return self::PAYMENT_INSTRUMENTS[$kind] ?? self::PAYMENT_INSTRUMENTS['obligation'];
    }
}
