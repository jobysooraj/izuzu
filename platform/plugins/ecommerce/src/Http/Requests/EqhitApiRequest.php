<?php
namespace Botble\Ecommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EqhitApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vin'   => 'nullable|string|max:30',

            // fig can be a string OR an array of strings
            'fig'   => 'nullable',
            'fig.*' => 'sometimes|string|max:50',

            'pno'   => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'name'  => 'nullable|string|max:255',
        ];
    }
}
