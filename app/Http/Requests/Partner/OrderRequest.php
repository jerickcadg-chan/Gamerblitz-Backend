<?php

namespace App\Http\Requests\Partner;

use App\Constants\PlatformConstant;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'partner_ref' => 'required',
            'item_code' => 'required',
            'cust_account' => 'required',
            'qty' => 'required|integer|min:1',
        ];
    }
}
