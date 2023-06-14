<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePropertyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'property' => 'required|array',
            'property.description' => 'required',
            'property.price' => 'required|numeric',
            'address' => 'required|array',
            'address.address' => 'required',
            'address.city' => 'required',
            'address.state' => 'required',
            'address.zip' => 'required',
            'address.country' => 'required',
            'user' => 'required|array',
            'user.first_name' => 'required',
            'user.last_name' => 'required',
            'user.email' => 'required',
            'files' => 'nullable|array',
            'files.*' => 'mimes:jpeg,png,pdf',
        ];
    }

    public function messages()
    {
        return [
            'property.required' => 'The property field is required.',
            'property.description.required' => 'The property description field is required.',
            'property.price.required' => 'The property price field is required.',

            'address.required' => 'The address field is required.',
            'address.address.required' => 'The address address field is required.',
            'address.city.required' => 'The address city field is required.',
            'address.state.required' => 'The address state field is required.',
            'address.zip.required' => 'The address zip field is required.',
            'address.country.required' => 'The address country field is required.',

            'user.required' => 'The user field is required.',
            'user.first_name.required' => 'The user first_name field is required.',
            'user.last_name.required' => 'The user last_name field is required.',
            'user.email.required' => 'The user email item field is required.',

            'files.array' => 'The files field must be an array.',
            'files.*.mimes' => 'The files must be in JPEG, PNG, or PDF format.',
        ];
    }

    protected function passedValidation()
    {
        if ($this->hasFile('files')) {
            $this->merge([
                'files' => $this->file('files'),
            ]);
        }
    }
}
