@php
$categories = collect(eqhit_fetch_categories())->groupBy('parent_key');
@endphp
<select {{ $attributes->merge(['name' => 'categories[]']) }} data-bb-toggle="init-categories-dropdown" data-url="{{ route('public.ajax.categories-dropdown') }}" aria-label="{{ __('Product categories') }}">
    <option value="">{{ __('All Categories') }}</option>
    @foreach ($categories->get(null) ?? [] as $category)
    <option value="{{ $category['key'] }}">{{ $category['name'] }}</option>
    @endforeach
</select>
