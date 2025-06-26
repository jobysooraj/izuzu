<?php
namespace Botble\Ecommerce\Repositories\Eloquent;

use Botble\Ecommerce\Repositories\Interfaces\EqhitInterface;


class EqhitRepository implements EqhitInterface
{

    public function searchParts(array $filters): array
    {
        return eqhit_fetch_parts($filters['vin'] ?? '',$filters['sp'] ?? null,$filters['sygt_mei'] ?? null,$filters['pno'] ?? null,$filters['fig'] ?? null,$filters['name'] ?? null);
    }
}
