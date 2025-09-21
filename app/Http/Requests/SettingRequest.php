<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'settings' => ['nullable','array'],
            'files.logo'       => ['nullable','image','mimes:jpg,jpeg,png,svg,gif,webp','max:5120'],
            'files.logo_alt'   => ['nullable','image','mimes:jpg,jpeg,png,svg,gif,webp','max:5120'],
            'files.favicon'    => ['nullable','image','mimes:jpg,jpeg,png,svg,gif,webp,ico','max:5120'],
            'files.popup_image'=> ['nullable','image','mimes:jpg,jpeg,png,svg,gif,webp','max:5120'],
        ];
    }
}
