<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Shift;

class StorePatrolScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'area_id' => ['required', Rule::exists('areas', 'id')->whereNull('deleted_at')],

            'shift_id' => ['required', Rule::exists('shifts', 'id')->whereNull('deleted_at')],
            'round_number' => ['required', 'integer', 'min:1', 'max:3'],

            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $start = $this->input('start_time');

                    if ($start === null || $start === '') {
                        return; // ditangani oleh rule start_time
                    }

                    if ($start === $value) {
                        $fail('Jam selesai tidak boleh sama dengan jam mulai.');

                        return;
                    }

                    // Shift yang melewati tengah malam boleh memakai end_time < start_time.
                    $shift = Shift::find($this->input('shift_id'));
                    if ($shift && $shift->start_time > $shift->end_time) {
                        return;
                    }

                    if ($value < $start) {
                        $fail('Jam selesai harus lebih lambat dari jam mulai.');
                    }
                },
            ],

            'status' => ['required', Rule::in(['scheduled', 'ongoing', 'completed', 'missed'])],

            'checkpoints' => ['required', 'array', 'min:1'],
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
            'round_number.integer' => 'Urutan putaran harus berupa angka.',
            'round_number.min' => 'Urutan putaran minimal adalah 1.',
            'round_number.max' => 'Urutan putaran maksimal adalah 3.',

            'checkpoints.required' => 'Minimal satu checkpoint harus dipilih untuk rute patroli ini.',
            'checkpoints.min' => 'Minimal satu checkpoint harus dipilih untuk rute patroli ini.',
            'checkpoints.*.exists' => 'Salah satu checkpoint yang dipilih tidak valid atau sudah tidak aktif.',

            'area_id.exists' => 'Area yang dipilih tidak valid.',
            'shift_id.required' => 'Shift wajib dipilih.',
            'shift_id.exists' => 'Shift yang dipilih tidak valid.',
            'status.in' => 'Status jadwal tidak valid.',
        ];
    }

    /**
     * Ubah format checkpoints menjadi array sederhana sebelum validasi
     * dan pastikan tidak ada duplikat.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('checkpoints')) {
            $this->merge([
                'checkpoints' => array_unique(array_values($this->checkpoints ?? [])),
            ]);
        }
    }
}
