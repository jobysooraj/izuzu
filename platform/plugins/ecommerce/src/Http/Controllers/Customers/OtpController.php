<?php
namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Customer;
use Carbon\Carbon;
use EmailHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class OtpController extends BaseController
{
    public function showForm(Request $request)
    {

        $email = $request->query('email');

        return view('plugins/ecommerce::customers.auth.verify-otp', [
            'email' => $request->get('email'),
        ]);
    }

    public function verifyOtp(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:ec_customers,email',
            'otp'   => 'required|digits:6',
        ]);

        $customer = Customer::where('email', $request->input('email'))->first();

        if (! $customer) {
            return Redirect::back()->withErrors(['email' => 'Invalid email address.']);
        }

        if ($customer->otp_code !== $request->input('otp')) {
            return Redirect::back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if (Carbon::parse($customer->otp_expires_at)->isPast()) {
            return Redirect::back()->withErrors(['otp' => 'OTP has expired.']);
        }

        $customer->is_otp_verified = true;
        $customer->otp_code        = null;
        $customer->otp_expires_at  = null;
        $customer->save();
        $plainPassword = Session::pull('otp_user_password');
        $roleName      = Session::pull('otp_user_role');

        // Send credentials email
        $templateKey = $roleName === 'vendor'
        ? 'dealer-credentials'
        : 'customer-credentials';

        $content = file_get_contents(platform_path('plugins/ecommerce/resources/email-templates/welcome.tpl'));

        if ($template = $request->input('template')) {
            [$type, $module, $template] = explode('.', $template);

            if ($type && $module && $template) {
                $content = get_setting_email_template_content($type, $module, $template);
            }
        }

        $content = get_setting_email_template_content('plugins', 'ecommerce', $templateKey);
        if (empty($content)) {
            $content = file_get_contents(platform_path('plugins/ecommerce/resources/email-templates/welcome.tpl'));
        }
        $content = str_replace(
            ['{{ customer_name }}', '{{ customer_email }}', '{{ customer_password }}', '{{ site_url }}'],
            [$customer->name, $customer->email, $plainPassword, theme_option('site_url') ?? url('/')],
            $content
        );
        //
        EmailHandler::send(
            $content,
            'Customer Registration',
            $request->input('email'),
            [],
            true
        );
        return redirect()->route('customer.login')->withSuccess('Email verified successfully. You can now log in.');
    }
}
