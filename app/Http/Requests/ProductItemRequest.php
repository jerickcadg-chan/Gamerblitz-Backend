<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'code'       => ['required', 'string', 'max:100'],
            'name'       => ['required', 'string', 'max:255'],
            'capital'    => ['required', 'numeric', 'min:0'],
            'stock'      => ['nullable', 'integer', 'min:0'],

            'margin'        => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_silver' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_gold'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_vip'    => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
