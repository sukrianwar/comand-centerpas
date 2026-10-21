<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckpointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checkpoint_name' => ['required', 'string', 'max:150'],
            'status'          => ['required', 'in:active,inactive'],
            'description'     => ['nullable', 'string', 'max:255'],
        ];
    }
}
