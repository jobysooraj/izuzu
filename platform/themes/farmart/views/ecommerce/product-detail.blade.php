
@include('plugins/ecommerce::themes.includes.product-detail')

{{-- @extends(Theme::getLayoutPath())

@section('content')
    <div class="container mt-4">
        <h2>{{ $product->name ?? 'No Name' }}</h2>

        <ul>
            <li><strong>Part No:</strong> {{ $product->pno ?? '-' }}</li>
            <li><strong>Fig:</strong> {{ $product->fig ?? '-' }}</li>
            <li><strong>Qty:</strong> {{ $product->qty ?? '-' }}</li>
            <li><strong>SYGT MEI:</strong> {{ $product->sygt_mei ?? '-' }}</li>
            <li><strong>Key:</strong> {{ $product->key ?? '-' }}</li>
            <li><strong>Production Year:</strong> {{ $product->p_year ?? '-' }}</li>
        </ul>

        <p>{!! $product->description ?? '' !!}</p>
    </div>
@endsection --}}
