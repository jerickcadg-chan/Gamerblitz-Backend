<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductItemCategoryMetaRequest extends FormRequest
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
        $picture =  'required|image';
        if ($this->isMethod('PUT')) {
            $picture = 'image';
        }
        return [
            'min_price' => 'integer|required',
            'picture' => $picture,
        ];
    }
}
