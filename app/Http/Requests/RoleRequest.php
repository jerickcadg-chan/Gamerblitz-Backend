<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
                    'name' => 'required|string|max:255|unique:roles',
                    'permission_id' => 'required'
                ];
            case 'PUT':
            case 'PATCH':
                return [
                    'name' => 'required|string|max:255|unique:roles,name,' .  request()->segment(2),
                    'permission_id' => 'required'
                ];

            default:
                break;

        }
    }
}
