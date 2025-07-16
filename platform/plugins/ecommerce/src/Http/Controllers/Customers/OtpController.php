<?php
namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Customer;
use Botble\Theme\Facades\Theme;
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
        return Theme::scope(
            'ecommerce.customers.auth.verify-otp',
            ['email' => $request->get('email')],
            'plugins/ecommerce::customers.auth.verify-otp' // fallback
        )->render();

    }

    public function verifyOtp(Request $request)
    {

        $email = $request->input('email') ?? Session::get('otp_customer_email');
        $request->merge(['email' => $email]);
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
        $credentials   = Session::pull('otp_credentials');

        if ($plainPassword && $roleName) {
            // This is a registration case (new account)
            $templateKey = $roleName === 'vendor' ? 'dealer-credentials' : 'customer-credentials';

            $content = get_setting_email_template_content('plugins', 'ecommerce', $templateKey)
            ?: file_get_contents(platform_path('plugins/ecommerce/resources/email-templates/welcome.tpl'));

            $content = str_replace(
                ['{{ customer_name }}', '{{ customer_email }}', '{{ customer_password }}', '{{ site_url }}'],
                [$customer->name, $customer->email, $plainPassword, theme_option('site_url') ?? url('/')],
                $content
            );

            EmailHandler::send($content, 'Customer Registration', $email, [], true);

            return redirect()->route('customer.login')->withSuccess('Email verified successfully. You can now log in.');
        }

        if ($credentials) {
            // This is a login case
            auth('customer')->attempt($credentials);

            return redirect()->intended(route('customer.overview'));
        }

        return redirect()->route('customer.login')->withSuccess('Email verified successfully. You can now log in.');
    }
    public function resendOtp(Request $request)
    {
        $email = $request->query('email') ?? Session::get('otp_customer_email');

        if (! $email) {
            return redirect()->route('customer.login')->withErrors(['email' => 'Email not found.']);
        }

        $customer = Customer::where('email', $email)->first();

        if (! $customer) {
            return redirect()->route('customer.login')->withErrors(['email' => 'Customer not found.']);
        }

        // Generate new OTP
        $otp                      = rand(100000, 999999);
        $customer->otp_code       = $otp;
        $customer->otp_expires_at = now()->addMinutes(5);
        $customer->save();

        // Send OTP again
        $content = get_setting_email_template_content('plugins', 'ecommerce', 'customer-otp')
        ?: file_get_contents(platform_path('plugins/ecommerce/resources/email-templates/customer-otp.tpl'));

        $content = str_replace(
            ['{{ customer_name }}', '{{ customer_otp }}', '{{ site_url }}'],
            [$customer->name, $otp, theme_option('site_url') ?? url('/')],
            $content
        );

        EmailHandler::send($content, 'OTP Verification', $email, [], true);

        return redirect()->route('customer.otp.verify', ['email' => $email])->withSuccess('OTP resent successfully.');
    }

}
