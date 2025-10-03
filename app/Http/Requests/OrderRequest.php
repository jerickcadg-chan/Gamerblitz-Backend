<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['nullable', 'email'],
            'cust_phone_number' => 'nullable',
            'product_item_id' => ['required', 'exists:product_items,id'],
            'qty' => ['required', 'integer'],
            'payment_method_id' => 'required|exists:payment_methods,id',
            'currency_code' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
        ];
    }
}
