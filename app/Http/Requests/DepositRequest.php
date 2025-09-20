<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
            'amount' => 'required',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'currency_code' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
        ];
    }
}
