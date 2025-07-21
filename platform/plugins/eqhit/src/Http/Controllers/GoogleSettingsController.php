<?php
namespace Botble\Eqhit\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class GoogleSettingsController extends BaseController
{
    public function edit()
    {
        page_title()->setTitle('Eqhit API Settings And Google API Key ');
        return view('plugins/eqhit::settings.form');
    }

    public function update(Request $request)
    {
        setting()->set([
            'google_api_key'     => $request->input('google_api_key'),
            'google_places_key'  => $request->input('google_places_key'),
            'google_maps_key'    => $request->input('google_maps_key'),
            'google_api_enabled' => $request->has('google_api_enabled') ? 1 : 0,

        ])->save();

        return redirect()->route('settings.eqhit')->with('success_msg', trans('core/base::notices.update_success_message'));
    }
}
