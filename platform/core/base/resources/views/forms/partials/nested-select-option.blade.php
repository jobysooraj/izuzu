@php
if (!isset($groupedOptions)) {
if (! $options instanceof \Illuminate\Support\Collection) {
$options = collect($options);
}

// Filter out non-array elements (e.g., strings)
$options = $options->filter(fn ($item) => is_array($item) && isset($item['key'], $item['name']));

$groupedOptions = $options->groupBy('parent_key');
}

$parentId = $parentId ?? null;
$currentOptions = $groupedOptions->get($parentId);
@endphp

@if($currentOptions)
@foreach ($currentOptions as $option)

{{-- <option value="{{ $option->id }}" @selected(is_array($selected) ? in_array($option->id, $selected) : $option->id == $selected)>{!! $indent !!}{{ $option->name }}</option> --}}
<option value="{{ $option['key'] }}" @selected(is_array($selected) ? in_array($option['key'], $selected) : $option['key']==$selected)>
    {!! $indent !!}{{ $option['name'] }}
</option>
@if ($groupedOptions->has($option['key']))
@include('core/base::forms.partials.nested-select-option', [
'options' => $groupedOptions,
'indent' => $indent . '&nbsp;&nbsp;',
'parentId' => $option['key'],
'selected' => $selected,
])
@endif
@endforeach
@endif
