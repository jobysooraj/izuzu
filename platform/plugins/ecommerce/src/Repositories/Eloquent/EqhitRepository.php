<?php
namespace Botble\Ecommerce\Repositories\Eloquent;

use Botble\Ecommerce\Repositories\Interfaces\EqhitInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EqhitRepository implements EqhitInterface
{

    // public function searchParts(array $filters): array
    // {
    //     $vin      = $filters['vin'] ?? '';
    //     $sygt_mei = $filters['sygt_mei'] ?? null;
    //     $pno      = $filters['pno'] ?? null;
    //     $fig      = $filters['fig'] ?? null;
    //     $name     = $filters['name'] ?? null;

    //     // Use single `sp` if provided, otherwise default to both types
    //     $spList = isset($filters['sp']) ? [$filters['sp']] : ['MaintenanceParts', 'ExteriorParts'];

    //     $allParts = collect();
    //     $errors   = [];

    //     foreach ($spList as $sp) {

    //         $response = eqhit_fetch_parts($vin, $sp, $sygt_mei, $pno, $fig, $name);

    //         if (! empty($response['error'])) {
    //             $errors[] = "[$sp] " . $response['error'];
    //         }

    //         $allParts = $allParts->merge($response['data'] ?? []);

    //     }

    //     // Manual filtering fallback
    //     $filterable = compact('sygt_mei', 'pno', 'fig', 'name');
    //     foreach ($filterable as $key => $val) {
    //         $val = strtolower($val ?? '');
    //         if (! empty($val)) {
    //             $allParts = $allParts->filter(fn($item) =>
    //                 str_contains(strtolower($item[$key] ?? ''), $val)
    //             );
    //         }
    //     }

    //     $filteredParts =   $filteredParts = $allParts
    //     // cast each array to an object:
    //     ->map(fn($item) => (object) $item)
    //     ->values();

    //     $perPage       = request()->get('per_page', 15);
    //     $page          = request()->get('page', 1);
    //     // $cleanQuery    = collect(request()->except('page'))
    //     //     ->map(function ($item) {
    //     //         return is_array($item) ? array_unique($item) : $item;
    //     //     })
    //     //     ->all();
    //     $cleanQuery = collect(request()->except('page'))
    //         ->filter(function ($value, $key) {
    //             if ($key === 'categories') {
    //                 // Only keep if it's a non-empty array with non-empty values
    //                 return is_array($value) && collect($value)->filter()->isNotEmpty();
    //             }
    //             return ! empty($value);
    //         })
    //         ->map(function ($item) {
    //             return is_array($item) ? array_values(array_unique(array_filter($item))) : $item;
    //         })
    //         ->all();
    //     // Always exclude pagination duplication
    //     $originalQuery = parse_url(request()->fullUrl(), PHP_URL_QUERY);
    //     parse_str($originalQuery, $cleanQuery);
    //     unset($cleanQuery['page']);
    //     $paginator = new LengthAwarePaginator(
    //         $filteredParts->forPage($page, $perPage)->values(),
    //         $filteredParts->count(),
    //         $perPage,
    //         $page,
    //         [
    //             'path'  => request()->url(),
    //             'query' => $cleanQuery,
    //         ]
    //     );

    //     return [
    //         'data'  => $paginator,
    //         'error' => empty($errors) ? null : implode(' | ', $errors),
    //     ];
    //     // return eqhit_fetch_parts($filters['vin'] ?? '',$filters['sp'] ?? null,$filters['sygt_mei'] ?? null,$filters['pno'] ?? null,$filters['fig'] ?? null,$filters['name'] ?? null);
    // }
    public function searchParts(array $filters): array
    {
        $vin      = $filters['vin'] ?? '';
        $sygt_mei = $filters['sygt_mei'] ?? null;
        $pno      = $filters['pno'] ?? null;
        $name     = $filters['name'] ?? null;

       if (! empty($filters['pno']) || ! empty($filters['name'])) {
            unset($filters['fig']);
        }
        $rawFigs = $filters['fig'] ?? [];
        $figs    = is_array($rawFigs) ? $rawFigs : array_filter(array_map('trim', explode(',', (string) $rawFigs)));
        $figs    = array_filter($figs);


        // Determine sources
        $spList = isset($filters['sp']) ? [$filters['sp']] : ['MaintenanceParts', 'ExteriorParts'];

        $allParts = collect();
        $errors   = [];

        // Fetch per source and per fig
        foreach ($spList as $sp) {
            if (count($figs) > 0) {
                foreach ($figs as $fig) {
                    $response = eqhit_fetch_parts($vin, $sp, $sygt_mei, $pno, $fig, $name);
                    if (! empty($response['error'])) {
                        $errors[] = "[$sp][$fig] " . $response['error'];
                    }
                    $allParts = $allParts->merge($response['data'] ?? []);
                }
            } else {

                $response = eqhit_fetch_parts($vin, $sp, $sygt_mei, $pno, null, $name);

                if (! empty($response['error'])) {
                    $errors[] = "[$sp] " . $response['error'];
                }
                $allParts = $allParts->merge($response['data'] ?? []);
            }
        }

        // Optional manual fallback filters (sygt_mei, pno, name)
        foreach (['sygt_mei' => $sygt_mei, 'pno' => $pno, 'name' => $name] as $key => $val) {
            $val = strtolower($val ?? '');
            if ($val !== '') {
                $allParts = $allParts->filter(fn($item) =>
                    str_contains(strtolower($item[$key] ?? ''), $val)
                );
            }
        }
        $filteredParts = $allParts
            ->map(fn($item) => (object) $item)
            ->values();

        // Pagination
        $page    = request()->get('page', 1);
        $perPage = request()->get('per_page', 15);

        // Clean up query for pagination links
        $cleanQuery = collect(request()->except('page'))
            ->filter(fn($v) => ! (is_array($v) && empty(array_filter($v))) && $v !== '')
            ->map(fn($v) => is_array($v) ? array_values(array_unique(array_filter($v))) : $v)
            ->all();

        $paginator = new LengthAwarePaginator(
            $filteredParts->forPage($page, $perPage),
            $filteredParts->count(),
            $perPage,
            $page,
            [
                'path'  => request()->url(),
                'query' => $cleanQuery,
            ]
        );
        return [
            'data'  => $paginator,
            'error' => empty($errors) ? null : implode(' | ', $errors),
        ];
    }

    public function findPartsByPno(string $pno): array
    {
        $spOptions = ['ExteriorParts', 'MaintenanceParts'];

        foreach ($spOptions as $sp) {
            $response = eqhit_fetch_parts('', $sp, null, $pno, null, null);
            dd($response);
            if (! empty($response['error'])) {
                continue;
            }

            $products = $response['data'] ?? [];

            $product = collect($products)
                ->filter(fn($item) => is_array($item) && ($item['pno'] ?? null) === $pno)
                ->map(fn($item) => (object) $item)
                ->first();

            if ($product) {
                return [
                    'data'  => $product,
                    'error' => null,
                ];
            }
        }

        return [
            'data'  => null,
            'error' => 'Product not found',
        ];
    }

}
