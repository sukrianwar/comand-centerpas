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
            'area_id'         => ['required', 'exists:areas,id'],
            'checkpoint_name' => ['required', 'string', 'max:150'],
            'latitude'        => ['required', 'numeric', 'between:-90,90'],
            'longitude'       => ['required', 'numeric', 'between:-180,180'],
            'radius'          => ['nullable', 'integer', 'min:5', 'max:500'],
            'status'          => ['required', 'in:active,inactive'],
            'description'     => ['nullable', 'string', 'max:255'],
        ];
    }
}
