@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<x-core::card>
    <x-core::form :url="route('settings.eqhit.update')" method="POST">
        <x-core::card.header>
            <x-core::card.title>Eqhit API Settings</x-core::card.title>
        </x-core::card.header>

        <x-core::card.body>
            <x-core::form-group>
                <label for="eqhit_api_url">API URL</label>
                <input type="text" class="form-control" name="eqhit_api_url" value="{{ setting('eqhit_api_url') }}">
            </x-core::form-group>

            <x-core::form-group>
                <label for="eqhit_api_key">API Key</label>
                <input type="text" class="form-control" name="eqhit_api_key" value="{{ setting('eqhit_api_key') }}">
            </x-core::form-group>

            <x-core::form-group>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="eqhit_enabled" name="eqhit_enabled" value="1" {{ setting('eqhit_enabled') == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="eqhit_enabled">Enable Eqhit API</label>
                </div>
            </x-core::form-group>
        </x-core::card.body>

        <x-core::card.footer>
            <x-core::button type="submit" color="primary">Save</x-core::button>
        </x-core::card.footer>
    </x-core::form>
</x-core::card>
{{-- Google API Settings --}}
<x-core::card class="mt-4">
    <x-core::form :url="route('settings.google.update')" method="POST">
        <x-core::card.header>
            <x-core::card.title>Google API Settings</x-core::card.title>
        </x-core::card.header>

        <x-core::card.body>
            <x-core::form-group>
                <label for="google_api_key">Google API Key</label>
                <input type="text" class="form-control" name="google_api_key" value="{{ setting('google_api_key') }}">
            </x-core::form-group>

            <x-core::form-group>
                <label for="google_places_key">Google Places API Key</label>
                <input type="text" class="form-control" name="google_places_key" value="{{ setting('google_places_key') }}">
            </x-core::form-group>

            <x-core::form-group>
                <label for="google_maps_key">Google Maps API Key</label>
                <input type="text" class="form-control" name="google_maps_key" value="{{ setting('google_maps_key') }}">
            </x-core::form-group>
            <x-core::form-group>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="google_api_enabled" name="google_api_enabled" value="1" {{ setting('google_api_enabled') == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="google_api_enabled">Enable Google API</label>
                </div>
            </x-core::form-group>

        </x-core::card.body>

        <x-core::card.footer>
            <x-core::button type="submit" color="primary">Save</x-core::button>
        </x-core::card.footer>
    </x-core::form>
</x-core::card>
@endsection
