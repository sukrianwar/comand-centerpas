<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'area_name'   => ['required', 'string', 'max:150'],
            'area_code'   => ['required', 'string', 'max:30', 'unique:areas,area_code'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'area_code.unique' => 'Kode area ini sudah digunakan, gunakan kode lain.',
        ];
    }
}
