@if ($products->count())
<div class="bb-quick-search-content">
    <div class="bb-quick-search-list">
        @foreach ($products as $product)
        @php
        $vin = request('q') ?? '';
        $generatedLink = route('public.products.detail', [
        'slug' => \Str::slug($product->name),
        'pno' => $product->pno,
        'q' => $vin,
        ]);
        @endphp

        <a class="bb-quick-search-item" href="{{ $generatedLink }}">
            <div class="bb-quick-search-item-image">
                <img src="{{ $product->fname }}" alt="{{ $product->name }}" loading="lazy" width="60" height="60" onerror="this.src='/images/placeholder.png'">
            </div>
            <div class="bb-quick-search-item-info">
                <div class="bb-quick-search-item-name">{{ $product->name }}</div>
                <div class="bb-quick-search-item-name">Fig: {{ $product->fig }}</div>
                <div class="bb-quick-search-item-name">Pno: {{ $product->pno }}</div>
            </div>
        </a>
        @endforeach
    </div>
</div>

<div class="bb-quick-search-view-all">
    {{-- <a href="{{ route('public.products') }}"
    onclick="event.preventDefault(); this.closest('.bb-form-quick-search').submit();">
    {{ __('View all results') }}
    </a> --}}
    <a href="{{ route('public.products.filter.ajax') }}?q={{ request('q') }}" onclick="event.preventDefault(); document.querySelector('.bb-form-quick-search').submit();">
        {{ __('View all results') }}
    </a>

</div>
@else
<div class="bb-quick-search-empty">
    {{ __('No results found!') }}
</div>
@endif
