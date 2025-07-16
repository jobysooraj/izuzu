<?php
namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Forms\Fronts\Auth\LoginForm;
use Botble\Ecommerce\Http\Requests\LoginRequest;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends BaseController
{
    use AuthenticatesUsers, LogoutGuardTrait {
        AuthenticatesUsers::attemptLogin as baseAttemptLogin;
    }

    public string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('customer.guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        SeoHelper::setTitle(__('Login'));

        Theme::breadcrumb()->add(__('Login'), route('customer.login'));

        if (! in_array(url()->previous(), [route('customer.login'), route('customer.register')])) {
            session(['url.intended' => url()->previous()]);
        }

        return Theme::scope(
            'ecommerce.customers.login',
            ['form' => LoginForm::create()],
            'plugins/ecommerce::themes.customers.login'
        )->render();
    }

    protected function guard()
    {
        return auth('customer');
    }

    public function login(LoginRequest $request)
    {
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to log in and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $this->sendFailedLoginResponse();
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $this->loggedOut($request);

        return redirect()->to(BaseHelper::getHomepageUrl());
    }

    // protected function attemptLogin(LoginRequest $request)
    // {
    //     if ($this->guard()->validate($this->credentials($request))) {
    //         $customer = $this->guard()->getLastAttempted();

    //         if (EcommerceHelper::isEnableEmailVerification() && empty($customer->confirmed_at)) {
    //             throw ValidationException::withMessages([
    //                 'confirmation' => [
    //                     __(
    //                         'The given email address has not been confirmed. <a href=":resend_link">Resend confirmation link.</a>',
    //                         [
    //                             'resend_link' => route('customer.resend_confirmation', ['email' => $customer->email]),
    //                         ]
    //                     ),
    //                 ],
    //             ]);
    //         }

    //         if ($customer->status->getValue() !== CustomerStatusEnum::ACTIVATED) {
    //             throw ValidationException::withMessages([
    //                 'email' => [
    //                     __('Your account has been locked, please contact the administrator.'),
    //                 ],
    //             ]);
    //         }

    //         return $this->baseAttemptLogin($request);
    //     }

    //     return false;
    // }
    protected function attemptLogin(LoginRequest $request)
    {
        if ($this->guard()->validate($this->credentials($request))) {
            $customer = $this->guard()->getLastAttempted();

            if (EcommerceHelper::isEnableEmailVerification() && empty($customer->confirmed_at)) {
                throw ValidationException::withMessages([
                    'confirmation' => [
                        __(
                            'The given email address has not been confirmed. <a href=":resend_link">Resend confirmation link.</a>',
                            ['resend_link' => route('customer.resend_confirmation', ['email' => $customer->email])]
                        ),
                    ],
                ]);
            }

            if ($customer->status->getValue() !== CustomerStatusEnum::ACTIVATED) {
                throw ValidationException::withMessages([
                    'email' => [
                        __('Your account has been locked, please contact the administrator.'),
                    ],
                ]);
            }

            // ✅ Generate OTP
            $otp                      = rand(100000, 999999);
            $customer->otp_code       = $otp;
            $customer->otp_expires_at = now()->addMinutes(5);
            $customer->save();

            // ✅ Send OTP email
            $content = get_setting_email_template_content('plugins', 'ecommerce', 'customer-otp')
            ?: file_get_contents(platform_path('plugins/ecommerce/resources/email-templates/customer-otp.tpl'));

            $content = str_replace(
                ['{{ customer_name }}', '{{ customer_otp }}', '{{ site_url }}'],
                [$customer->name, $customer->otp_code, theme_option('site_url') ?? url('/')],
                $content
            );

            \EmailHandler::send(
                $content,
                'OTP Verification',
                $customer->email,
                [],
                true
            );

            // ✅ Store temp session
            session([
                'otp_customer_id'    => $customer->id,
                'otp_customer_email' => $customer->email,
                'otp_credentials'    => $this->credentials($request),
            ]);

            // ✅ Redirect to OTP form
            return redirect()->route('customer.otp.verify');
        }

        return false;
    }

    public function credentials(LoginRequest $request): array
    {
        $usernameKey = match (EcommerceHelper::getLoginOption()) {
            'phone'          => 'phone',
            'email_or_phone' => $request->isEmail($request->input($this->username())) ? 'email' : 'phone',
            default          => 'email',
        };

        return [
            $usernameKey => $request->input($this->username()),
            'password'   => $request->input('password'),
        ];
    }
}
