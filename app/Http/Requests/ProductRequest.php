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
                    'name' => 'required|max:255',
                    'code' => 'required|max:100',
                    'company' => 'required|max:100',
                    'product_category_id' => 'required',
                    'description' => 'required',
                    'how_to_order' => 'required',
                    'input_format' => 'nullable',
                    'status' => 'required',
                    'picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                    'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                ];
            case 'PUT':
            case 'PATCH':
                return [
                    'name' => 'required|max:255',
                    'code' => 'required|max:100',
                    'company' => 'required|max:100',
                    'product_category_id' => 'required',
                    'description' => 'required',
                    'how_to_order' => 'required',
                    'input_format' => 'required',
                    'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                    'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                    'status' => 'required',
                ];

            default:
                break;

        }
    }
}
