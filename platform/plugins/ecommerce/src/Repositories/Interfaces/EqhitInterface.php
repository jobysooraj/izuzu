<?php

namespace Botble\Ecommerce\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
interface EqhitInterface
{
    // Define any custom methods here, for example:
    public function searchParts(array $filters): array;
}
