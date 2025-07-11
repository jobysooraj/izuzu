<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (! function_exists('eqhit_fetch_parts')) {
    /**
     * Fetch parts from EqHit API.
     *
     * @param string $vin
     * @param string|null $sp
     * @param string|null $sygt_mei
     * @param string|null $pno
     * @param string|array|null $fig
     * @param string|null $name
     * @return array ['data' => array, 'error' => string|null]
     */
    function eqhit_fetch_parts(
        string $vin,
        ?string $sp = null,
        ?string $sygt_mei = null,
        ?string $pno = null,
        ?string $fig = null,
        ?string $name = null
    ): array {
        $url = 'https://eqhitproxy.gmi-projects.com/eqhit-proxy.aspx';

        $query = array_filter([
            'vin'      => $vin,
            'sp'       => $sp,
            'sygt_mei' => $sygt_mei,
            'pno'      => $pno,
            'fig'      => is_array($fig) ? ($fig[0] ?? null) : $fig,
            'name'     => $name,
        ]);

        try {
            $response = Http::timeout(10)
                ->retry(3, 200)
                ->get($url, $query);

            if (! $response->successful()) {
                Log::warning('EqHit API failed', [
                    'query'  => $query,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return ['data' => [], 'error' => 'API returned status ' . $response->status()];
            }
            $allowedFigs = is_array($fig)
            ? array_filter(array_map('trim', $fig))
            : (is_string($fig) && $fig !== '' ? [trim($fig)] : []);
            $items = collect($response->json())
                ->map(fn($item) => [
                    'fig'      => trim($item['fig'] ?? ''),
                    'key'      => trim($item['key'] ?? ''),
                    'name'     => trim($item['name'] ?? ''),
                    'lr'       => trim($item['lr'] ?? ''),
                    'pno'      => trim($item['pno'] ?? ''),
                    'qty'      => trim($item['qty'] ?? ''),
                    'fname'    => $item['fname'] ?? null,
                    'p_year'   => trim($item['p_year'] ?? ''),
                    'sygt_mei' => trim($item['sygt_mei'] ?? ''),
                ])
                ->filter(function ($item) use ($allowedFigs) {
                    return empty($allowedFigs) || in_array($item['fig'], $allowedFigs, true);
                })
                ->values()
                ->toArray();

            return ['data' => $items, 'error' => null];
        } catch (\Throwable $e) {
            Log::error('EqHit API exception', [
                'query'     => $query,
                'exception' => $e->getMessage(),
            ]);

            return ['data' => [], 'error' => 'Server exception during API request'];
        }
    }
}
if (! function_exists('eqhit_fetch_categories')) {
    /**
     * Get static categories for EqHit parts.
     *
     * @return array
     */
    function eqhit_fetch_categories(): array
    {
        return [
            ['key' => 'ExteriorParts', 'name' => 'Exterior Parts', 'parent_key' => null],
            ['key' => 'MaintenanceParts', 'name' => 'Maintenance Parts', 'parent_key' => null],
        ];
    }
}
if (! function_exists('eqhit_get_functional_system_categories')) {
    /**
     * Get Functional System Categories (Static).
     *
     * @return array
     */
    function eqhit_get_functional_system_categories(): array
    {
        $mainCategories = [
            '0' => 'Engine, Emission, Engine Electrical',
            '1' => 'Fuel Tank, Cooling, Air Intake, Exhaust System',
            '2' => 'Clutch, Gearbox, Transmission Axle',
            '3' => 'Brakes, Brake System',
            '4' => 'Drive Shaft, Axle, Steering, Suspension',
            '5' => 'Chassis, Cab Mounting',
            '6' => 'Exterior Bodywork, Window Support, Door Trim, Molding',
            '7' => 'Interior Trim, Logos, Labels, Seats, Mirrors',
            '8' => 'Chassis Electrical, Heating, A/C, Wipers, Radio',
            '9' => 'Tools',
        ];
        return $mainCategories;
        // $mainCategories = [
        //     '0' => 'Engine, Emission, Engine Electrical',
        //     '1' => 'Fuel Tank, Cooling, Air Intake, Exhaust System',
        //     '2' => 'Clutch, Gearbox, Transmission Axle',
        //     '3' => 'Brakes, Brake System',
        //     '4' => 'Drive Shaft, Axle, Steering, Suspension',
        //     '5' => 'Chassis, Cab Mounting',
        //     '6' => 'Exterior Bodywork, Window Support, Door Trim, Molding',
        //     '7' => 'Interior Trim, Logos, Labels, Seats, Mirrors',
        //     '8' => 'Chassis Electrical, Heating, A/C, Wipers',
        //     '9' => 'Others',
        // ];
        // $allFigures = eqhit_get_functional_system_categories(); // expected to return: [['fig' => '010', 'name' => 'GASKET; HD TO COVER'], ...]

        // $grouped = [];

        // foreach ($allFigures as $item) {
        //     $fig  = $item['fig'] ?? null;
        //     $name = $item['name'] ?? null;

        //     if (! $fig || strlen($fig) < 3) {
        //         continue;
        //     }

        //     $mainKey = substr($fig, 0, 1); // e.g., "0" from "010"

        //     if (! isset($mainCategories[$mainKey])) {
        //         continue;
        //     }

        //     if (! isset($grouped[$mainKey])) {
        //         $grouped[$mainKey] = [
        //             'label'    => $mainCategories[$mainKey],
        //             'children' => [],
        //         ];
        //     }

        //     $grouped[$mainKey]['children'][$fig] = $name;
        // }

        // return $grouped;
    }
}
