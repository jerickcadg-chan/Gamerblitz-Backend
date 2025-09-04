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
            'files.logo' => ['nullable','image','max:5120'],
            'files.logo_alt' => ['nullable','image','max:5120'],
            'files.favicon' => ['nullable','image','max:5120'],
            'files.popup_image' => ['nullable','image','max:5120'],
        ];
    }
}
