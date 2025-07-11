   @if($product)
<div class="col">
    <div class="product-inner">
        {!! Theme::partial('ecommerce.product-item', compact('product')) !!}
    </div>
</div>
@else
<div class="col-12 w-100">
        <div class="alert alert-warning mt-4 w-100" role="alert">

            {{ __(':total Product(s) found', ['total' => 0]) }}
        </div>
    </div>
@endif
