<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'id' => 'sometimes|integer',
            'category' => 'sometimes|array',
            'category.name' => 'sometimes|string|max:255',
            'photoUrls' => 'sometimes|array',
            'photoUrls.*' => 'sometimes|string|url',
            'tags' => 'sometimes|array',
            'tags.*.name' => 'sometimes|string|max:255'
        ];

        if ($this->isMethod('POST')) {
            $rules['name'] = 'required|string|max:255';
            $rules['status'] = 'required|in:available,pending,sold';
        } else {
            $rules['name'] = 'sometimes|string|max:255';
            $rules['status'] = 'sometimes|in:available,pending,sold';
        }

        return $rules;
    }


}
