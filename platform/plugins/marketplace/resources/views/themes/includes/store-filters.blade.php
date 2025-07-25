@php
    $currentMainFilterUrl = $store->url;

    $categories = ProductCategoryHelper::getProductCategoriesWithUrl();
    $categoriesRequest = (array) request()->input('categories', []);
    $categoryId = Arr::get($categoriesRequest, 0);
@endphp

<div class="bb-filter-offcanvas-area">
    <div class="bb-filter-offcanvas-wrapper">
        <div class="bb-filter-offcanvas-close">
            <button type="button" class="bb-filter-offcanvas-close-btn" data-bb-toggle="toggle-filter-sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
                {{ __('Close') }}
            </button>
        </div>

        <div class="bb-shop-sidebar">
            {{-- class="bb-product-form-filter" --}}
            <form action="{{ URL::current() }}" method="GET" >
                @include(EcommerceHelper::viewPath('includes.filters.filter-hidden-fields'))
                <input name="categories[]" type="hidden" value="{{ $categoryId }}">

                @include(EcommerceHelper::viewPath('includes.filters.search'))
                @include(EcommerceHelper::viewPath('includes.filters.categories'))
            </form>
        </div>
    </div>
</div>
