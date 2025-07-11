<input {{ $attributes->merge([
    'type' => 'search',
    'name' => 'q',
    'placeholder' => __('Search VIN...'),
    'value' => BaseHelper::stringify(request()->query('q')),
    'autocomplete' => 'off',
]) }}>
