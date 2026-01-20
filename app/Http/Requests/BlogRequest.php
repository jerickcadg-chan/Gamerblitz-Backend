<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest
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
        $id = $this->route('blog')?->id;

        return [
            'title'             => ['required','string','max:190'],
            'slug'              => ['nullable','string','max:200', Rule::unique('blogs','slug')->ignore($id)],
            'blog_category_id'  => ['required','exists:blog_categories,id'],
            'content'           => ['required','string'],
            'meta_description'  => ['nullable','string'],
            'status'            => ['required', Rule::in(['draft','published'])],
            'thumbnail'         => ['nullable','image','max:5120'],
            'thumbnail_url'     => ['nullable','url'],
        ];
    }
}
