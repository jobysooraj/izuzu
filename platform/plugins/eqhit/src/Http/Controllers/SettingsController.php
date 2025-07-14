<?php
namespace Botble\Eqhit\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Botble\Setting\Facades\Setting;

class SettingsController extends BaseController
{
    public function edit()
    {
        page_title()->setTitle('Eqhit API Settings');
        return view('plugins/eqhit::settings.form');
    }

    public function update(Request $request)
    {
        Setting::set('eqhit_api_url', $request->input('eqhit_api_url'));
        Setting::set('eqhit_api_key', $request->input('eqhit_api_key'));
        Setting::set('eqhit_enabled', $request->has('eqhit_enabled') ? 1 : 0);

        Setting::save();

        return redirect()->route('settings.eqhit')->with('success_msg', 'Settings saved!');
    }
}
