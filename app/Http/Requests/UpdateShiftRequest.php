<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shift = $this->route('shift');
        $shiftId = $shift instanceof \App\Models\Shift ? $shift->id : $shift;

        return [
            'shift_name' => [
                'required', 'string', 'max:100',
                Rule::unique('shifts', 'shift_name')->ignore($shiftId),
            ],
            'shift_code' => [
                'nullable', 'string', 'max:30',
                Rule::unique('shifts', 'shift_code')->ignore($shiftId),
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'different:start_time'],
            'status'     => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'shift_name.required' => 'Nama shift wajib diisi.',
            'shift_name.unique'   => 'Nama shift ini sudah digunakan.',
            'shift_code.unique'   => 'Kode shift ini sudah digunakan, gunakan kode lain.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'end_time.required'   => 'Jam selesai wajib diisi.',
            'end_time.different'  => 'Jam selesai tidak boleh sama dengan jam mulai.',
            'status.in'           => 'Status shift tidak valid.',
        ];
    }
}
