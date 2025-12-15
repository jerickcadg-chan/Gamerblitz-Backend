<?php

namespace App\Http\Requests\Partner;

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
            'qty' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function fieldInputs(): object
    {
        $paymentMethod = PaymentMethod::where('slug', PaymentMethod::BALANCE)->first();

        return (object) [
            'partner_ref' => $this->partner_ref,
            'product_item_id' => $this->item_code,
            'cust_account' => $this->cust_account,
            'qty' => $this->qty,
            'payment_method_id' => $paymentMethod->id,
        ];
    }
}
