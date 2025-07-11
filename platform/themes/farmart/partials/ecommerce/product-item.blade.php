@php
$useNamedRoute = $useNamedRoute ?? true;
// $vin=request('q')?? '';
// $generatedLink =url('/products/' . \Str::slug($product->name) . '-' . $product->pno . '?q=' . $vin);
// if (!empty($product->pno)){

// $generatedLink =route('public.products.detail', ['slug' => \Str::slug($product->name), 'pno' => $product->pno, 'q' => $vin]);
// }

@endphp
<div class="product-thumbnail">
    {{-- {{ url('/products/' . \Str::slug($product->name)) }} --}}

    @if (!empty($product->pno))
    <a class="product-loop__link img-fluid-eq" href="#" tabindex="0">
        <div class="img-fluid-eq__dummy"></div>
        <div class="img-fluid-eq__wrap">
            {{-- <img class="lazyload product-thumbnail__img" data-src="{{ RvMedia::getImageUrl($product['fname'], 'small', false, RvMedia::getDefaultImage()) }}" src="{{ image_placeholder($product['fname'], 'small') }}" alt="{{ $product['name'] }}"> --}}
            <img class="lazyload product-thumbnail__img" data-src="{{ isset($product->fname) ? RvMedia::getImageUrl($product->fname, 'small', false, RvMedia::getDefaultImage()) : '/images/placeholder.png' }}" src="{{ isset($product->fname) ? image_placeholder($product->fname, 'small') : '/images/placeholder.png' }}" alt="{{ $product->name ?? 'Unnamed' }}">

        </div>

    </a>
    @endif

</div>
<div class="product-details position-relative">
    <div class="product-content-box">

        <h3 class="product__title">
            <a href="#" tabindex="0">{{ $product->name }}</a>
        </h3>
        <ul class="list-unstyled text-muted small mb-3">
            @if (!empty($product->pno))
            <li><strong>{{ __('Part No:') }}</strong> {{ $product->pno }}</li>
            @endif

            @if (!empty($product->fig))
            <li><strong>{{ __('Fig:') }}</strong> {{ $product->fig }}</li>
            @endif

            @if (!empty($product->qty))
            <li><strong>{{ __('Qty:') }}</strong> {{ $product->qty }}</li>
            @endif

            @if (!empty($product->sygt_mei))
            <li><strong>{{ __('SYGT MEI:') }}</strong> {{ $product->sygt_mei }}</li>
            @endif

            @if (!empty($product->key))
            <li><strong>{{ __('Key:') }}</strong> {{ $product->key }}</li>
            @endif

            @if (!empty($product->p_year))
            <li><strong>{{ __('Production Year:') }}</strong> {{ $product->p_year }}</li>
            @endif
        </ul>

    </div>
    <div class="product-bottom-box">
        {{-- {!! Theme::partial('ecommerce.product-cart-form', compact('product')) !!} --}}
    </div>
</div>
