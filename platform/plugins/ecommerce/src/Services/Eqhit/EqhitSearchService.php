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

        $filters = $request->only([
            'vin', 'sp', 'sygt_mei', 'pno', 'fig', 'name',
        ]);

        return $this->eqhitRepository->searchParts($filters);
    }
}
