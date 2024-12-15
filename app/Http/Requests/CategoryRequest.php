<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $rules = [];
        if ($this->input('category_edit_id') != "") {
            $rules['name'] = ['required', 'alpha:ascii', 'max:50', Rule::unique('categories')->ignore($this->input('category_edit_id')), ];
        } else {
            $rules['name'] =  ['required', 'alpha:ascii', 'max:50', Rule::unique('categories')];
        }

        return $rules;
    }
}
