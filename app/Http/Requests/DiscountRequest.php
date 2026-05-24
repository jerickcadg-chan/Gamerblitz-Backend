<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiscountRequest extends FormRequest
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
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'required|string|max:255',
                    'code' => 'nullable|unique:discounts|max:10',
                    'disc_type' => ['required', Rule::in(config('array.discount.disc_type_validation'))],
                    'nominal' => 'required',
                    'maximum' => 'required|integer|min:1',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'product_type' => ['required', Rule::in(config('array.discount.product_type_validation'))]
                ];
            case 'PUT':
            case 'PATCH':
                return [
                    'name' => 'required|string|max:255',
                    'code' => 'nullable|max:10|unique:discounts,code,' .  $this->route('discount')->id,
                    'disc_type' => ['required', Rule::in(config('array.discount.disc_type_validation'))],
                    'nominal' => 'required',
                    'maximum' => 'required|integer|min:1',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'product_type' => ['required', Rule::in(config('array.discount.product_type_validation'))]
                ];

            default:
                break;

        }
    }
}
