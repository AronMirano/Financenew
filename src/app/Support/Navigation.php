<?php

namespace App\Support;

/**
 * Navigation model for the sidebar + breadcrumbs.
 * `icon` names map to the inline SVG sprite in resources/views/partials/icon.blade.php.
 */
class Navigation
{
    /** @var list<array{label: string, items: list<array{route: string, label: string, icon: string}>}> */
    public const NAV = [
        [
            'label' => 'General',
            'items' => [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
            ],
        ],
        [
            'label' => 'PRE — Receipts & Expenditures',
            'items' => [
                ['route' => 'pre.overview', 'label' => 'Overview', 'icon' => 'clipboard-list'],
                ['route' => 'pre.submit', 'label' => 'Submit PRE', 'icon' => 'file-text'],
                ['route' => 'pre.ppmp', 'label' => 'PPMP Import', 'icon' => 'import'],
                ['route' => 'pre.expenditures', 'label' => 'Expenditures', 'icon' => 'wallet'],
                ['route' => 'pre.obrs.index', 'label' => 'OBRS', 'icon' => 'receipt'],
                ['route' => 'pre.burs.index', 'label' => 'BURS', 'icon' => 'scroll-text'],
                ['route' => 'pre.workingpaper', 'label' => 'Working Paper', 'icon' => 'table-2'],
                ['route' => 'pre.saob', 'label' => 'SAOB', 'icon' => 'file-spreadsheet'],
                ['route' => 'pre.monitoring', 'label' => 'Monitoring', 'icon' => 'bar-chart-3'],
                ['route' => 'pre.reports', 'label' => 'Reports', 'icon' => 'file-text'],
                ['route' => 'pre.analytics', 'label' => 'Analytics', 'icon' => 'bar-chart-3'],
            ],
        ],
        [
            'label' => 'Annualization',
            'items' => [
                ['route' => 'ann.overview', 'label' => 'Overview', 'icon' => 'clipboard-list'],
                ['route' => 'ann.import', 'label' => 'Import Salary', 'icon' => 'import'],
                ['route' => 'ann.records', 'label' => 'Salary Records', 'icon' => 'table-2'],
                ['route' => 'ann.employee', 'label' => 'Employee Details', 'icon' => 'users'],
                ['route' => 'ann.deductions', 'label' => 'Deductions', 'icon' => 'wallet'],
                ['route' => 'ann.reports', 'label' => 'Reports', 'icon' => 'file-text'],
                ['route' => 'ann.analytics', 'label' => 'Analytics', 'icon' => 'bar-chart-3'],
            ],
        ],
    ];

    /** @var array<string, array{module: string, title: string}> */
    public const PAGE_TITLES = [
        'dashboard' => ['module' => 'General', 'title' => 'Dashboard'],
        'pre.overview' => ['module' => 'PRE', 'title' => 'Overview'],
        'pre.submit' => ['module' => 'PRE', 'title' => 'Submit PRE'],
        'pre.ppmp' => ['module' => 'PRE', 'title' => 'PPMP Import'],
        'pre.expenditures' => ['module' => 'PRE', 'title' => 'Expenditures'],
        'pre.obrs.index' => ['module' => 'PRE', 'title' => 'OBRS'],
        'pre.obrs.create' => ['module' => 'PRE', 'title' => 'New OBR'],
        'pre.obrs.show' => ['module' => 'PRE', 'title' => 'OBRS'],
        'pre.burs.index' => ['module' => 'PRE', 'title' => 'BURS'],
        'pre.burs.create' => ['module' => 'PRE', 'title' => 'New BUR'],
        'pre.burs.show' => ['module' => 'PRE', 'title' => 'BURS'],
        'pre.workingpaper' => ['module' => 'PRE', 'title' => 'Working Paper'],
        'pre.saob' => ['module' => 'PRE', 'title' => 'SAOB'],
        'pre.monitoring' => ['module' => 'PRE', 'title' => 'Monitoring'],
        'pre.reports' => ['module' => 'PRE', 'title' => 'Reports'],
        'pre.analytics' => ['module' => 'PRE', 'title' => 'Analytics'],
        'ann.overview' => ['module' => 'Annualization', 'title' => 'Overview'],
        'ann.import' => ['module' => 'Annualization', 'title' => 'Import Salary'],
        'ann.records' => ['module' => 'Annualization', 'title' => 'Salary Records'],
        'ann.employee' => ['module' => 'Annualization', 'title' => 'Employee Details'],
        'ann.deductions' => ['module' => 'Annualization', 'title' => 'Deductions'],
        'ann.reports' => ['module' => 'Annualization', 'title' => 'Reports'],
        'ann.analytics' => ['module' => 'Annualization', 'title' => 'Analytics'],
    ];

    /** @return array{module: string, title: string} */
    public static function meta(?string $route): array
    {
        return self::PAGE_TITLES[$route] ?? ['module' => 'General', 'title' => 'Dashboard'];
    }

    /**
     * A sidebar item is active for its own route and for any sibling route in
     * the same resource group (e.g. pre.obrs.show highlights the OBRS item).
     */
    public static function isActive(string $itemRoute, ?string $currentRoute): bool
    {
        if ($currentRoute === null) {
            return false;
        }
        if ($itemRoute === $currentRoute) {
            return true;
        }

        $group = preg_replace('/\.(index|create|store|show|edit|update|destroy)$/', '', $itemRoute);

        return $group !== $itemRoute && str_starts_with($currentRoute, $group.'.');
    }
}
