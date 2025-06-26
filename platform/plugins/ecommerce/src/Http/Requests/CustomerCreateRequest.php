<?php
namespace Botble\Ecommerce\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Ecommerce\Models\Customer;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CustomerCreateRequest extends Request
{
    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'min:2', 'max:120'],
            'email'         => ['required', new EmailRule(), Rule::unique((new Customer())->getTable(), 'email')],
            'password'      => ['required', 'string', 'min:6', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/',
            ],
            'dob'           => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
            'private_notes' => ['nullable', 'string', 'max:1000'],
            'phone'         => [
                'nullable',
                'regex:/^[0-9]{8,15}$/',
                Rule::unique('ec_customers', 'phone')->ignore($this->route('id')),
            ],

        ];
    }
}
