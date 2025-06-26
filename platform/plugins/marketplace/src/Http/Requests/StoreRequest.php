<?php
namespace Botble\Marketplace\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\EmailRule;
use Botble\Base\Rules\MediaImageRule;
use Botble\Marketplace\Models\Store;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class StoreRequest extends Request
{
    public function rules(): array
    {
        $storeId = $this->route('store')?->id ?? $this->input('id');

        $fileRules = $storeId
        ? ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']  // ✅ update (optional)
        : ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']; // ✅ create (required)
        return [
            'name'               => ['required', 'max:250', 'min:2'],
            'email'              => [
                'required',
                new EmailRule(),
                Rule::unique((new Store())->getTable(), 'email')
                    ->ignore($storeId),
            ],
            'phone'              => 'required|' . BaseHelper::getPhoneValidationRule(),
            'slug'               => ['required', 'string', 'max:255'],
            'customer_id'        => ['required', 'string', 'exists:ec_customers,id'],
            'description'        => ['nullable', 'max:400', 'string'],
            'status'             => Rule::in(BaseStatusEnum::values()),
            'company'            => ['nullable', 'string', 'max:255'],
            'tax_id'             => [
                'required',
                'string',
                'max:255',
                Rule::unique((new Store())->getTable(), 'tax_id')
                    ->ignore($storeId),
            ],

            'zip_code'           => ['nullable', ...BaseHelper::getZipcodeValidationRule(true)],
            'logo'               => ['nullable', 'string', new MediaImageRule([], null, 5 * 1024 * 1024)],
            'logo_square'        => ['nullable', 'string', new MediaImageRule([], null, 5 * 1024 * 1024)],
            'cover_image'        => ['nullable', 'string', new MediaImageRule([], null, 5 * 1024 * 1024)],

            'certificate_file'   => $fileRules,
            'government_id_file' => $fileRules,
        ];
    }
}
