<?php
namespace Botble\Ecommerce\Repositories\Interfaces;

interface EqhitInterface
{
    // Define any custom methods here, for example:
    public function searchParts(array $filters): array;
    // EqhitInterface.php
    public function findPartsByPno(string $pno): array;

    // public function getProducts(array $params, array $filters = []);
    // public function getProductsWithCategory(array $params);

}
