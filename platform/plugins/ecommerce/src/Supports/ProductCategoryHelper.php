<?php
namespace Botble\Ecommerce\Supports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Language\Facades\Language;
use Botble\Support\Services\Cache\Cache;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductCategoryHelper
{
    protected Collection $allCategories;

    protected Collection $treeCategories;

    public function getAllProductCategories(array $params = [], bool $onlyParent = false): Collection
    {
        // if (! isset($this->allCategories)) {
        //     $query = ProductCategory::query();

        //     if (! empty($conditions = Arr::get($params, 'condition', []))) {
        //         $query = $query->where($conditions);
        //     }

        //     if (! empty($with = Arr::get($params, 'with', []))) {
        //         $query = $query->with($with);
        //     }

        //     if (! empty($withCount = Arr::get($params, 'withCount', []))) {
        //         $query = $query->withCount($withCount);
        //     }

        //     if ($onlyParent) {
        //         $query = $query->where(function ($query): void {
        //             $query
        //                 ->whereNull('parent_id')
        //                 ->orWhere('parent_id', 0);
        //         });
        //     }

        //     $query = $query
        //         ->orderBy('order')->latest();

        //     if ($select = Arr::get($params, 'select', [
        //         'id',
        //         'name',
        //         'status',
        //         'is_featured',
        //         'image',
        //     ])) {
        //         $query = $query->select($select);
        //     }

        //     $this->allCategories = $query->get();
        // }

        // return $this->allCategories;
        if (! isset($this->allCategories)) {
            $categories = eqhit_fetch_categories();

            // Convert array to Laravel Collection of stdClass or objects if needed
            $this->allCategories = collect(array_map(function ($item) {
                return (object) $item;
            }, $categories));
        }

        return $this->allCategories;
    }

    /**
     * @deprecated
     */
    public function getAllProductCategoriesSortByChildren(): Collection
    {
        return $this->getTreeCategories();
    }

    /**
     * @deprecated
     */
    public function getAllProductCategoriesWithChildren(): array
    {
        return $this->getTreeCategories()->toArray();
    }

    /**
     * @deprecated
     */
    public function getProductCategoriesWithIndent(): Collection
    {
        return $this->getActiveTreeCategories();
    }

    public function getActiveTreeCategories(): Collection
    {
        return $this->getTreeCategories(true);
    }

    public function getTreeCategories(bool $activeOnly = false, array $select = ['*']): Collection
    {
        // if (! isset($this->treeCategories)) {
        //     $this->treeCategories = $this->getAllProductCategories(
        //         [
        //             'condition' => $activeOnly ? ['status' => BaseStatusEnum::PUBLISHED] : [],
        //             'with'      => [$activeOnly ? 'activeChildren' : 'children' => function ($query) use ($select): void {
        //                 $query->select($select ?: '*');
        //             }],
        //         ],
        //         true
        //     );
        // }

        // return $this->treeCategories;
        return $this->getAllProductCategories();
    }

    public function getTreeCategoriesOptions(array | Collection $categories, array $options = [], ?string $indent = null): array
    {
        if (! $categories instanceof Collection) {
            foreach ($categories as $category) {
                $options[$category['id']] = $indent . $category['name'];

                if (! empty($category['active_children']) || ! empty($category['children'])) {
                    $options = $this->getTreeCategoriesOptions($category['active_children'] ?? $category['children'], $options, $indent . '&nbsp;&nbsp;');
                }
            }

            return $options;
        }

        foreach ($categories as $category) {
            $options[$category->id] = $indent . $category->name;

            if (! empty($category->activeChildren)) {
                $options = $this->getTreeCategoriesOptions(
                    $category->activeChildren,
                    $options,
                    $indent . '&nbsp;&nbsp;'
                );
            }
        }

        return $options;
    }

    public function renderProductCategoriesSelect(array | int | string | null $selected = null): string
    {
        $cache = Cache::make(ProductCategory::class);

        $cacheKey = 'ecommerce_categories_for_rendering_select' . md5($cache->generateCacheKeyFromInput() . serialize(func_get_args()));

        if ($cache->has($cacheKey)) {
            $categories = $cache->get($cacheKey);
        } else {
            $query = ProductCategory::query()
                ->toBase()
                ->where('status', BaseStatusEnum::PUBLISHED)
                ->select([
                    'ec_product_categories.id',
                    'ec_product_categories.name',
                    'parent_id',
                ])
                ->oldest('order')
                ->latest();

            $categories = $this->applyQuery($query)->get();

            $cache->put($cacheKey, $categories, Carbon::now()->addHours(2));
        }

        return view('core/base::forms.partials.nested-select-option', [
            'options'  => $categories,
            'indent'   => null,
            'selected' => $selected,
        ])->render();
    }

    public function getProductCategoriesWithUrl(array $categoryIds = [], array $condition = [], ?int $limit = null): Collection
    {
        $categories = eqhit_fetch_categories();

        return collect(array_map(function ($cat) {

            return (object) [
                'id'         => $cat['id'] ?? null,
                'name'       => $cat['name'],
                'parent_id'   => $cat['parent_id'] ?? null, // Ensure this is present
                'url'        => route('public.products', ['categories[]' => $cat['key']]),
                'icon'       => null,
                'image'      => null,
                'icon_image' => null,
            ];
        }, $categories));
        // $cache = Cache::make(ProductCategory::class);

        // $cacheKey = 'ecommerce_categories_for_widgets_' . md5($cache->generateCacheKeyFromInput() . serialize(func_get_args()));

        // if ($cache->has($cacheKey)) {
        //     return $cache->get($cacheKey);
        // }

        // $tablePrefix = Schema::getConnection()->getTablePrefix();
        // $query       = ProductCategory::query()
        //     ->toBase()
        //     ->where('status', BaseStatusEnum::PUBLISHED)
        //     ->select([
        //         'ec_product_categories.id',
        //         'ec_product_categories.name',
        //         'ec_product_categories.order',
        //         'parent_id',
        //         DB::raw("
        //             CONCAT({$tablePrefix}slugs.prefix,
        //             IF({$tablePrefix}slugs.prefix IS NOT NULL AND {$tablePrefix}slugs.prefix != '', '/', ''),
        //             {$tablePrefix}slugs.key) as url
        //         "),
        //         'icon',
        //         'image',
        //         'icon_image',
        //     ])
        //     ->leftJoin('slugs', function (JoinClause $join): void {
        //         $join
        //             ->on('slugs.reference_id', 'ec_product_categories.id')
        //             ->where('slugs.reference_type', ProductCategory::class);
        //     })
        //     ->when($this->isEnabledMultiLanguages(), function (Builder $query): void {
        //         $query
        //             ->leftJoin('slugs_translations as st', function (JoinClause $join): void {
        //                 $join
        //                     ->on('st.slugs_id', 'slugs.id')
        //                     ->where('st.lang_code', Language::getCurrentLocaleCode());
        //             })
        //             ->addSelect(
        //                 DB::raw("
        //                     IF(
        //                         st.key IS NOT NULL,
        //                         CONCAT(st.prefix, IF(st.prefix IS NOT NULL AND st.prefix != '', '/', ''), st.key),
        //                         CONCAT(slugs.prefix, IF(slugs.prefix IS NOT NULL AND slugs.prefix != '', '/', ''), slugs.key)
        //                     ) as url
        //                 ")
        //             );
        //     })
        //     ->oldest('ec_product_categories.order')
        //     ->when(
        //         ! empty($categoryIds),
        //         fn(Builder $query) => $query->whereIn('ec_product_categories.id', $categoryIds)
        //     )
        //     ->when($limit > 0, fn($query) => $query->limit($limit))
        //     ->when($condition, fn($query) => $query->where($condition));

        // $query = $this->applyQuery($query);

        // $categories = $query->get()->unique('id');

        // $cache->put($cacheKey, $categories, Carbon::now()->addHours(2));

        // return $categories;
    }

    public function applyQuery(Builder $query): Builder
    {
        if ($this->isEnabledMultiLanguages()) {
            return $query
                ->leftJoin('ec_product_categories_translations as ct', function (JoinClause $join): void {
                    $join
                        ->on('ec_product_categories_id', 'ec_product_categories.id')
                        ->where('ct.lang_code', Language::getCurrentLocaleCode());
                })
                ->addSelect(DB::raw('IF(ct.name IS NOT NULL, ct.name, ec_product_categories.name) as name'));
        }

        return $query;
    }

    protected function isEnabledMultiLanguages(): bool
    {
        return is_plugin_active('language') &&
        is_plugin_active('language-advanced') &&
        Language::getCurrentLocale() !== Language::getDefaultLocale();
    }
}
