<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
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
        $id = $this->route('payment_method')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                Rule::unique('payment_methods', 'slug')->ignore($id),
            ],
            'account_name' => ['nullable', 'string', 'max:250'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'account_holder_name' => ['nullable', 'string', 'max:250'],

            'admin_fee' => ['required', 'numeric', 'min:0'],
            'admin_type' => ['required', Rule::in(['nominal', 'percentage', 'no-admin'])],

            'vendor' => ['required', 'string', 'max:50'],
            'category' => ['required', 'string', 'max:100'],

            'is_active' => ['required', 'boolean'],
            'ordering' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
