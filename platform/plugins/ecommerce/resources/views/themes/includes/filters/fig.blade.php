@php
$vin = request('q');
$partSources = ['ExteriorParts', 'MaintenanceParts'];
$selectedFigs = is_array(request('fig')) ? request('fig') : explode(',', (string) request('fig'));

// Fetch parts from all sources
$allParts = collect($partSources)
->flatMap(fn($source) => eqhit_fetch_parts($vin, $source)['data'] ?? [])
->all();

// Get defined system categories
$mainCategories = eqhit_get_functional_system_categories();

// Initialize grouped structure
$grouped = collect($mainCategories)->map(fn($label, $key) => [
'label' => $label,
'children' => [],
])->all();

// Populate grouped with fig data
foreach ($allParts as $item) {
$fig = $item['fig'] ?? null;
$name = trim($item['name'] ?? '');

if (empty($fig)) continue;

$mainKey = substr($fig, 0, 1);
if (!isset($grouped[$mainKey])) continue;

$grouped[$mainKey]['children'][$fig] ??= $name;
}
@endphp

<input type="hidden" id="fig-input" name="fig" value="{{ implode(',', $selectedFigs) }}">

@foreach ($grouped as $systemId => $group)
@php
$childKeys = array_keys($group['children']);
$childFigs = implode(',', $childKeys); // Now a string
$allSelected = !empty($childKeys) && collect($childKeys)->every(fn($f) => in_array($f, $selectedFigs));

$query = array_merge(request()->query(), ['fig' => $childFigs]);
@endphp

<div class="fig-group" style="margin-bottom:1.5rem;">
    <div class="fig-heading" data-target="fig-children-{{ $systemId }}" style="cursor:pointer;">
        <label for="">
            <input type="checkbox" class="fig-parent-checkbox" name="fig[]" value="{{ $childFigs }}" {{ $allSelected ? 'checked' : '' }}>
            {{ $group['label'] }}
        </label>
    </div>


    @if (count($group['children']))
    <div id="fig-children-{{ $systemId }}" class="fig-children" style="display:none;padding-left:1rem;">
        <div style="display:flex;flex-wrap:wrap;gap:0.75rem;">
            @foreach ($group['children'] as $fig => $figLabel)
            <label style="display:inline-flex;align-items:center;gap:0.3rem;background:#f8f8f8;padding:6px 10px;border-radius:6px;">
                <input type="checkbox" name="fig[]" class="fig-checkbox" value="{{ $fig }}" {{ in_array($fig, $selectedFigs) ? 'checked' : '' }}>
                <span>{{ $fig }} – {{ $figLabel }}</span>
            </label>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endforeach
