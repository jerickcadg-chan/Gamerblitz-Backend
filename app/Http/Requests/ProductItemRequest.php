<?php

namespace App\Http\Requests;

use App\Models\ProductItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $productItemId = $this->route('product_item');

        return [
            'product_id' => ['required', 'exists:products,id'],
            'code'       => [
                'required',
                'string',
                'max:100',
                $this->isMethod('post')
                    ? Rule::unique('product_items', 'code')
                    : Rule::unique('product_items', 'code')->ignore($productItemId),
            ],
            'name'       => ['required', 'string', 'max:255'],
            'capital'    => ['required', 'numeric', 'min:0'],
            'stock'      => ['nullable', 'integer', 'min:0'],

            'margin'        => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_silver' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_gold'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'margin_vip'    => ['nullable', 'numeric', 'min:0', 'max:100'],

            'status' => [
                'nullable',
                'string',
                Rule::in([
                    ProductItem::STATUS_ACTIVE,
                    ProductItem::STATUS_EMPTY,
                    ProductItem::STATUS_NON_ACTIVE,
                    ProductItem::STATUS_TROUBLE,
                ]),
            ],
        ];
    }
}
