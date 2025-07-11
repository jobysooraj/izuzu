<?php
namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Http\Controllers\BaseController;
use Botble\Ecommerce\Services\Eqhit\EqhitSearchService;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PublicAjaxController extends BaseController
{
    // public function ajaxSearchProducts(Request $request, GetProductService $productService)
    // {

    //     $request->merge(['num' => 12]);

    //     $with = EcommerceHelper::withProductEagerLoadingRelations();

    //     $products = $productService->getProduct($request, null, null, $with);

    //     $queries = $request->input();

    //     foreach ($queries as $key => $query) {
    //         if (! $query || $key == 'num' || (is_array($query) && ! Arr::get($query, 0))) {
    //             unset($queries[$key]);
    //         }
    //     }

    //     $total = $products->count();

    //     return $this
    //         ->httpResponse()
    //         ->setData(view(EcommerceHelper::viewPath('includes.ajax-search-results'), compact('products', 'queries'))->render())
    //         ->setMessage($total != 1 ? __(':total Products found', compact('total')) : __(':total Product found', compact('total')));
    // }
    public function ajaxSearchProducts(Request $request, EqhitSearchService $service)
    {

        $request->merge(['num' => 12]);

        $queries = $request->input();
        foreach ($queries as $key => $query) {
            if (! $query || $key == 'num' || (is_array($query) && ! Arr::get($query, 0))) {
                unset($queries[$key]);
            }
        }
        // Call Eqhit API via service

        $response = $service->searchParts($request);
        $paginator = $response['data'];

        $error = $response['error'];
        $data = collect($paginator->items());

        $total = $data->count();

        return $this
            ->httpResponse()
            ->setData(view(EcommerceHelper::viewPath('includes.ajax-search-results'), [
                'products' => $data,
                'queries'  => $queries,
                'error'    => $response['error'],
            ])->render())
            ->setMessage($total != 1 ? __(':total Products found', compact('total')) : __(':total Product found', compact('total')));

    }
    public function ajaxGetCategoriesDropdown()
    {

        $categories = eqhit_fetch_categories();

        // Optional: convert to associative array for <option> dropdown
        $options = [];
        foreach ($categories as $category) {
            $options[$category['key']] = $category['name'];
        }

        $categoriesDropdownView = Theme::getThemeNamespace('partials.product-categories-dropdown');

        return $this
            ->httpResponse()
            ->setData([
                'select'   => view('core/base::forms.partials.nested-select-option', [
                    'options'  => $options,
                    'selected' => null,
                    'indent'   => null,
                ])->render(),
                'dropdown' => view()->exists($categoriesDropdownView)
                ? view($categoriesDropdownView, compact('categories'))->render()
                : null,
            ]);
    }
    // public function ajaxGetCategoriesDropdown()
    // {

    //     $categoriesDropdownView = Theme::getThemeNamespace('partials.product-categories-dropdown');

    //     return $this
    //         ->httpResponse()
    //         ->setData([
    //             'select'   => ProductCategoryHelper::renderProductCategoriesSelect(),
    //             'dropdown' => view()->exists($categoriesDropdownView)
    //             ? view($categoriesDropdownView)->render()
    //             : null,
    //         ]);
    // }
}
