<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
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
                    'serial_number' => 'required|string',
                    'password' => 'required|string',
                    'capital' => 'required'
                ];
            case 'PUT':
            case 'PATCH':
                return [
                    'serial_number' => 'required|string',
                    'capital' => 'required'
                ];

            default:
                break;

        }
    }
}
