<?php
namespace Botble\ACL\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Support\Http\Requests\Request;

class ResetPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            'token'    => ['required', 'string'],
            'email'    => ['required', new EmailRule()],
            'password' => ['required',
                'string',
                'min:6',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/'],
        ];
    }
}
