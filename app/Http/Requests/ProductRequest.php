<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'category_id' => ['required'],
            'price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/']
        ];
        
        if ($this->input('product_edit_id') != "") {
            $rules['name'] = ['required', 'alpha:ascii', 'max:50', Rule::unique('products')->ignore($this->input('product_edit_id')), ];
        } else {
            $rules['name'] =  ['required', 'alpha:ascii', 'max:50', Rule::unique('products')];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'price.required' => 'Price is required',
            'price.numeric' => 'Please enter a valid number',
            'price.regex' => 'Price must have at most two decimal places',
            'price.min' => "Price must be at least 0.01.",
            'price.max' => "Price must not exceed 10000000.",
            'category_id.required' => "Please select category",
            'name.required' => 'Name field is required',
            'name.max' => 'Please enter less than 50 character',
        ];
    }
}
