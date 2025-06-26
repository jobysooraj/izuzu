<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Repositories\Interfaces\EqhitInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

if (!function_exists('eqhit_fetch_parts')) {
    /**
     * Fetch parts from EqHit API.
     *
     * @param string $vin
     * @param string|null $sp
     * @param string|null $model
     * @param string|null $pno
     * @param string|null $fig
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
            'fig'      => $fig,
            'name'     => $name,
        ]);

        try {
            $response = Http::timeout(10)
                ->retry(3, 200)
                ->get($url, $query);

            if (!$response->successful()) {
                Log::warning('EqHit API failed', [
                    'query' => $query,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return ['data' => [], 'error' => 'API returned status ' . $response->status()];
            }

            $items = collect($response->json())->map(fn($item) => [
                'fig'      => trim($item['fig'] ?? ''),
                'key'      => trim($item['key'] ?? ''),
                'name'     => trim($item['name'] ?? ''),
                'lr'       => trim($item['lr'] ?? ''),
                'pno'      => trim($item['pno'] ?? ''),
                'qty'      => trim($item['qty'] ?? ''),
                'fname'    => $item['fname'] ?? null,
                'p_year'   => trim($item['p_year'] ?? ''),
                'sygt_mei' => trim($item['sygt_mei'] ?? ''),
            ])->toArray();

            return ['data' => $items, 'error' => null];
        } catch (\Throwable $e) {
            Log::error('EqHit API exception', [
                'query' => $query,
                'exception' => $e->getMessage(),
            ]);

            return ['data' => [], 'error' => 'Server exception during API request'];
        }
    }
}
