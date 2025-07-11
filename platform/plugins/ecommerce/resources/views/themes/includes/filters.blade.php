@if (EcommerceHelper::hasAnyProductFilters())
@php
$dataForFilter = EcommerceHelper::dataForFilter($category ?? null, $request ?? null);
[$categories, $brands, $tags, $rand, $categoriesRequest, $urlCurrent, $categoryId, $maxFilterPrice] = $dataForFilter;
@endphp

{{-- <div class="bb-shop-sidebar">
    {{-- class="bb-product-form-filter no-ajax" commented for ajax result getting
    <form action="{{ route('public.products.filter.ajax') }}" data-action="{{ route('public.products.filter.ajax') }}" method="GET" >
@include(EcommerceHelper::viewPath('includes.filters.filter-hidden-fields'))

{!! apply_filters('theme_ecommerce_products_filter_before', null, $dataForFilter) !!}
{{-- <div class="bb-product-filter-section mb-4">
            <h4 class="bb-product-filter-title">{{ __('Search by Part Details') }}</h4>
<div class="bb-product-filter-content">
    <input type="hidden" name="quick_search" value="1" />

    <input type="text" name="pno" value="{{ request('pno') }}" class="form-control mb-2" placeholder="Search by Part No (Pno)">
    <input type="text" name="fig" value="{{ request('fig') }}" class="form-control mb-2" placeholder="Search by Fig">
    <input type="text" name="name" value="{{ request('name') }}" class="form-control mb-2" placeholder="Search by Name">
    <input type="text" name="sygt_mei" value="{{ request('sygt_mei') }}" class="form-control mb-2" placeholder="Search by Model">
    <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">{{ __('Search') }}</button>
</div>
</div> --}}

{{-- @if (EcommerceHelper::isEnabledFilterProductsByCategories())
        @include(EcommerceHelper::viewPath('includes.filters.categories'))
        @endif --}}

{{-- @if (EcommerceHelper::isEnabledFilterProductsByBrands())
                @include(EcommerceHelper::viewPath('includes.filters.brands'))
            @endif --}}

{{-- @if (EcommerceHelper::isEnabledFilterProductsByTags())
                @include(EcommerceHelper::viewPath('includes.filters.tags'))
            @endif

            @if (EcommerceHelper::isEnabledFilterProductsByPrice() && (! EcommerceHelper::hideProductPrice() || EcommerceHelper::isCartEnabled()))
                @include(EcommerceHelper::viewPath('includes.filters.price'))
            @endif

            @if (EcommerceHelper::isEnabledFilterProductsByAttributes())
                @include(EcommerceHelper::viewPath('includes.filters.attributes', ['view' => $view ?? null]))
            @endif

        {!! apply_filters('theme_ecommerce_products_filter_after', null, $dataForFilter) !!}
    </form>
</div> --}}
<div class="bb-shop-sidebar">
    {{-- {{ route('public.products.filter.ajax') }} --}}
    {{-- class="bb-product-form-filter" --}}
    <form class="bb-product-form-filter" action="{{ route('public.products.filter.ajax') }}" data-action="{{ route('public.products.filter.ajax') }}" method="GET">
        @include(EcommerceHelper::viewPath('includes.filters.filter-hidden-fields'))

        {!! apply_filters('theme_ecommerce_products_filter_before', null, $dataForFilter) !!}

        @if (EcommerceHelper::isEnabledFilterProductsByBrands())
        @include(EcommerceHelper::viewPath('includes.filters.fig'))
        @endif


        {!! apply_filters('theme_ecommerce_products_filter_after', null, $dataForFilter) !!}
    </form>
</div>
@endif
