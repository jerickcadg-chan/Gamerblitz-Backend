<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountUpdateRequest extends FormRequest
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
        $rules = [
            'title' => 'required',
            'code' => ['required', 'unique:accounts,code,' . $this->route('account')->id],
            'description' => ['required'],
            'winrate' => ['required', 'numeric', 'min:0', 'max:100'],
            'skin' => ['required', 'numeric', 'min:0'],
            'heroes' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'cover_picture' => ['nullable', 'array'],
            'cover_picture.*' => ['nullable', 'image', 'max:2048'],
        ];

        if ($this->input('discount') === "1" || $this->input('discount') === 1 || $this->input('discount') === true) {
            $rules['discount_type'] = ['required', 'in:percentage,nominal'];
            $rules['discount_amount'] = ['required', 'numeric', 'min:0'];
            if ($this->input('discount_type') === 'percentage') {
                $rules['discount_amount'][] = 'max:100';
            }
        }


        return $rules;
    }
}
