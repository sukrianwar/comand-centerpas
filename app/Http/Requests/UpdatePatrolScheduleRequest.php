<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatrolScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'schedule_name' => ['required', 'string', 'max:150'],

            'area_id'  => ['required', Rule::exists('areas', 'id')->whereNull('deleted_at')],

            'shift'        => ['required', Rule::in(['pagi', 'malam'])],
            'round_number' => ['required', 'integer', 'min:1', 'max:3'],

            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],

            'interval' => ['nullable', 'integer', 'min:1'],

            'status' => ['required', Rule::in(['scheduled', 'ongoing', 'completed', 'missed'])],

            'checkpoints'   => ['required', 'array', 'min:1'],
            'checkpoints.*' => [
                'integer',
                Rule::exists('checkpoints', 'id')->whereNull('deleted_at')->where('status', 'active'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'round_number.required' => 'Urutan putaran patroli (ke-1, ke-2, atau ke-3) wajib diisi.',
            'round_number.integer'  => 'Urutan putaran harus berupa angka.',
            'round_number.min'      => 'Urutan putaran minimal adalah 1.',
            'round_number.max'      => 'Urutan putaran maksimal adalah 3.',

            'end_time.after'        => 'Jam selesai harus lebih lambat dari jam mulai.',

            'checkpoints.required'  => 'Minimal satu checkpoint harus dipilih untuk rute patroli ini.',
            'checkpoints.min'       => 'Minimal satu checkpoint harus dipilih untuk rute patroli ini.',
            'checkpoints.*.exists'  => 'Salah satu checkpoint yang dipilih tidak valid atau sudah tidak aktif.',

            'area_id.exists'        => 'Area yang dipilih tidak valid.',
            'shift.in'              => 'Shift harus berupa Pagi atau Malam.',
            'status.in'             => 'Status jadwal tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('checkpoints')) {
            $this->merge([
                'checkpoints' => array_unique(array_values($this->checkpoints ?? [])),
            ]);
        }
    }
}
