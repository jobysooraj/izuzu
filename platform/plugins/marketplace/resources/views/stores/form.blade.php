@extends($layout ?? BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
@php
$hasMoreThanOneLanguage = count(\Botble\Base\Supports\Language::getAvailableLocales()) > 1;
@endphp
<x-core::card>
    <x-core::card.header>
        <x-core::tab class="card-header-tabs">
            <x-core::tab.item id="information-tab" :label="trans('plugins/marketplace::store.store')" :is-active="true" />
            @if($store && $store->customer->is_vendor)
            @include('plugins/marketplace::customers.tax-info-tab')
            @include('plugins/marketplace::customers.payout-info-tab')
            @if ($hasMoreThanOneLanguage)
            <x-core::tab.item id="tab_preferences" :label="__('Preferences')" />
            @endif
            @endif
            {!! apply_filters(BASE_FILTER_REGISTER_CONTENT_TABS, null, $store) !!}
            {!! apply_filters('marketplace_vendor_settings_register_content_tabs', null, $store) !!}
        </x-core::tab>
    </x-core::card.header>

    <x-core::card.body>
        <x-core::tab.content>
            <x-core::tab.pane id="information-tab" :is-active="true">
                @if (isset($store) && $store->id)
                {!! Form::open(['route' => ['marketplace.store.update', $store->id], 'method' => 'POST', 'class' => 'form form-save']) !!}
                @else
                {!! Form::open(['route' => 'marketplace.store.create', 'method' => 'POST', 'class' => 'form form-save']) !!}
                @endif

                {!! $form !!}

                {!! Form::close() !!}

            </x-core::tab.pane>
            @if($store && $store->customer->is_vendor)
            @include('plugins/marketplace::customers.tax-form', ['model' => $store->customer])
            @include('plugins/marketplace::customers.payout-form', ['model' => $store->customer])

            @if ($hasMoreThanOneLanguage)
            <x-core::tab.pane id="tab_preferences">
                {!! \Botble\Marketplace\Forms\Vendor\LanguageSettingForm::createFromModel($store->customer)->renderForm() !!}
            </x-core::tab.pane>
            @endif
            @endif
            {!! apply_filters(BASE_FILTER_REGISTER_CONTENT_TAB_INSIDE, null, $store) !!}
            {!! apply_filters('marketplace_vendor_settings_register_content_tab_inside', null, $store) !!}
        </x-core::tab.content>
    </x-core::card.body>
</x-core::card>
@stop
