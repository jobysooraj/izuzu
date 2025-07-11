{{-- <div class="bb-ecommerce-filter-hidden-fields">
    @foreach ([
    'layout',
    'page',
    'per-page',
    'num',
    'sort-by',
    'collection',
    ] as $item)
    <input name="{{ $item }}" type="hidden" class="product-filter-item" value="{{ BaseHelper::stringify(request()->input($item)) }}">
@endforeach

@if (request()->has('collections'))
@php
$collections = EcommerceHelper::parseFilterParams(request(), 'collections');
@endphp
@foreach ($collections as $collection)
<input name="collections[]" type="hidden" class="product-filter-item" value="{{ $collection }}">
@endforeach
@endif

@if (request()->has('categories') && ! isset($category))
@php
$categories = EcommerceHelper::parseFilterParams(request(), 'categories');
@endphp
@foreach ($categories as $category)
<input name="categories[]" type="hidden" class="product-filter-item" value="{{ $category }}">
@endforeach
@endif
@foreach (['q', 'pno', 'fig', 'name', 'key','sygt_mei'] as $searchParam)
@if (request()->filled($searchParam))
<input type="hidden" name="{{ $searchParam }}" class="product-filter-item" value="{{ request($searchParam) }}">
@endif
@endforeach
</div> --}}
<div class="bb-ecommerce-filter-hidden-fields">
    @foreach ([
    'layout',
    'page',
    'per-page',
    'num',
    'sort-by',
    'collection',
    ] as $item)
    <input name="{{ $item }}" type="hidden" class="product-filter-item" value="{{ BaseHelper::stringify(request()->input($item)) }}">
    @endforeach

    {{-- Handle collections --}}
    @php
    $collections = EcommerceHelper::parseFilterParams(request(), 'collections');
    @endphp
    @foreach ($collections as $collection)
    <input name="collections[]" type="hidden" class="product-filter-item" value="{{ $collection }}">
    @endforeach

    {{-- Handle categories safely, always parsing --}}
    @php
    $categories = EcommerceHelper::parseFilterParams(request(), 'categories');
    @endphp
    @if (!empty($categories))
    @foreach (request('categories', []) as $category)
    @if (!empty($category))
    <input type="hidden" name="categories[]" class="product-filter-item" value="{{ $category }}">
    @endif
    @endforeach
    @endif

    {{-- Handle search params --}}
    @foreach (['q', 'pno', 'fig', 'name','sygt_mei'] as $searchParam)
    @php
    $value = request($searchParam);

    // If it's an array (like 'fig[]'), convert to comma-separated string
    if (is_array($value)) {
    $value = implode(',', $value);
    }
    @endphp

    @if (!empty($value))
    <input type="hidden" name="{{ $searchParam }}" class="product-filter-item" value="{{ $value }}">
    @endif
    @endforeach
</div>
