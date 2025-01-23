<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
                    'category' => 'required|string',
                    'description' => 'required|string',
                    'how_to_order' => 'required|string',
                    'status' => 'required',
                    'product_joki' => 'required_if:category,joki',
//                    'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];
            case 'PUT':
            case 'PATCH':
                return [
                    'name' => 'required|string|max:255',
                    'category' => 'required|string',
                    'description' => 'required|string',
                    'how_to_order' => 'required|string',
                    'product_joki' => 'required_if:category,joki',
//                    'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];

            default:
                break;

        }
    }
}
