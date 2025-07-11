<?php
namespace Botble\Ecommerce\Services\Eqhit;

use Botble\Ecommerce\Repositories\Interfaces\EqhitInterface;
use Illuminate\Http\Request;

class EqhitSearchService
{
    protected EqhitInterface $eqhitRepository;

    public function __construct(EqhitInterface $eqhitRepository)
    {
        $this->eqhitRepository = $eqhitRepository;
    }

    /**
     * Search parts from Eqhit based on request input.
     */
    public function searchParts(Request $request): array
    {

        if ($request->filled('q')) {
            $request->merge(['vin' => $request->input('q')]);
        }
        if ($request->filled('categories')) {
            $categories = $request->input('categories');

            // For example, use the first one (or handle multiple)
            if (is_array($categories)) {
                $request->merge([
                    'sp' => $categories[0], // OR 'sp' => $categories[0]
                ]);
            }
        }

        $filters = $request->only([
            'vin', 'sp', 'sygt_mei', 'pno', 'fig', 'name',
        ]);

        return $this->eqhitRepository->searchParts($filters);
    }
    public function findPartsByPno(string $pno): array
    {

            $response = $this->eqhitRepository->findPartsByPno($pno);

            if (!empty($response['error'])) {
                return null;
            }

            $products = $response['data'] instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $response['data']->items()
                : (array) $response['data'];

            $product = collect($products)
                ->filter(fn($item) => is_array($item) && ($item['pno'] ?? null) === $pno)
                ->map(fn($item) => (object) $item)
                ->first();

            if ($product) {
                return $product;
            }


        return null;
    }

}
