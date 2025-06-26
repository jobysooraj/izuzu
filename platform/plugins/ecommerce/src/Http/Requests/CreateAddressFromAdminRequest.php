<?php
namespace Botble\Ecommerce\Http\Requests;

use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Support\Http\Requests\Request;

class CreateAddressFromAdminRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'phone'       => ['nullable', 'regex:/^[0-9]{10,15}$/'],
            'email'       => ['required', 'email', 'max:255'],
            'is_default'  => ['integer', 'min:0', 'max:1'],
            'customer_id' => ['required', 'exists:ec_customers,id'],
        ];

        if (! EcommerceHelper::isUsingInMultipleCountries()) {
            $this->merge(['country' => EcommerceHelper::getFirstCountryId()]);
        }

        return array_merge($rules, EcommerceHelper::getCustomerAddressValidationRules());
    }
}
