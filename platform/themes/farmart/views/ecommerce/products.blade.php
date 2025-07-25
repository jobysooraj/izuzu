@php
Theme::layout('full-width');

@endphp

{!! $widgets = dynamic_sidebar('products_list_sidebar') !!}

@if (empty($widgets))
{!! Theme::partial('page-header', ['size' => 'xxxl', 'withTitle' => false]) !!}
@endif

<div class="container-xxxl">
    <div class="row my-3 my-md-5">
        <div class="col-12">
            <div class="row catalog-header justify-content-between">
                <div class="col-auto catalog-header__left d-flex align-items-center">
                    <h1 class="h2 catalog-header__title d-none d-lg-block">{{ SeoHelper::getTitleOnly() }}</h1>

                    @if (EcommerceHelper::hasAnyProductFilters())

                    <a class="d-lg-none sidebar-filter-mobile" href="#">
                        <span class="svg-icon me-2">
                            <svg>
                                <use href="#svg-icon-filter" xlink:href="#svg-icon-filter"></use>
                            </svg>
                        </span>
                        <span>{{ __('Filter') }}</span>
                    </a>
                    @endif
                </div>
                <div class="col-auto catalog-header__right">
                    <div class="catalog-toolbar row align-items-center">
                        <form method="GET"  class="bb-product-form-filter d-flex align-items-end gap-2 flex-wrap" data-action="{{ route('public.products.filter.ajax') }}"  action="{{ route('public.products.filter.ajax') }}" >
                            {{-- @csrf --}}
                            @include(EcommerceHelper::viewPath('includes.filters.filter-hidden-fields'))

                            <div>
                                <input type="text" name="pno" value="{{ request('pno') }}" class="form-control" placeholder="Search by Part No (Pno)">
                            </div>

                            <div>
                                <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Search by Name">
                            </div>

                            <div>
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                            </div>
                            <div>
                                <a href="{{route('public.index')}}" class="btn btn-sm btn-primary">{{ __('Clear') }}</a>
                            </div>
                        </form>
                        {{-- @include(Theme::getThemeNamespace('views.ecommerce.includes.sort'))
                        @include(Theme::getThemeNamespace('views.ecommerce.includes.layout')) --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (Theme::get('pageDescription'))
    <div class="ps-block__content">
        <div class="ps-section__content">
            {!! BaseHelper::clean(Theme::get('pageDescription')) !!}
        </div>
    </div>
    @endif

    <div class="row">
        @if (EcommerceHelper::hasAnyProductFilters())
        <div class="col-xxl-2 col-lg-3">
            <aside class="catalog-primary-sidebar catalog-sidebar" data-toggle-target="product-categories-primary-sidebar">
                <div class="backdrop"></div>

                <div class="catalog-sidebar--inner side-left">
                    @include(EcommerceHelper::viewPath('includes.filters'))
                </div>
            </aside>
        </div>
        @endif

        <div @class(['products-listing position-relative bb-product-items-wrapper col-12', 'col-xxl-10 col-lg-9'=> EcommerceHelper::hasAnyProductFilters()])>
            {{-- @include(Theme::getThemeNamespace('views.ecommerce.includes.product-items')) --}}
            @include(Theme::getThemeNamespace('views.ecommerce.includes.product-items'), ['error' => $error ?? null])

        </div>
    </div>
</div>

