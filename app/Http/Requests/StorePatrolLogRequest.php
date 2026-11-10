<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatrolLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Middleware 'auth' sudah memastikan user login.
        // Middleware 'restrict.guard' memastikan hanya Security Guard yang akses.
        return true;
    }

    public function rules(): array
    {
        return [
            'patrol_schedule_id' => [
                'required',
                'integer',
                Rule::exists('patrol_schedules', 'id')->whereNull('deleted_at'),
            ],

            'checkpoint_id' => [
                'required',
                'integer',
                Rule::exists('checkpoints', 'id')->whereNull('deleted_at')->where('status', 'active'),
            ],

            // Foto bukti: wajib 3–5 foto, image, max 5 MB per foto
            'photos' => ['required', 'array', 'min:3', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'],

            // Catatan opsional
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'patrol_schedule_id.required' => 'ID jadwal patroli wajib diisi.',
            'patrol_schedule_id.exists' => 'Jadwal patroli tidak ditemukan atau sudah dihapus.',
            'checkpoint_id.required' => 'ID checkpoint wajib diisi.',
            'checkpoint_id.exists' => 'Checkpoint tidak ditemukan, tidak aktif, atau sudah dihapus.',
            'photos.required' => 'Foto bukti wajib diunggah.',
            'photos.array' => 'Foto bukti tidak valid.',
            'photos.min' => 'Foto bukti minimal 3 foto.',
            'photos.max' => 'Foto bukti maksimal 5 foto.',
            'photos.*.required' => 'Foto bukti wajib diunggah.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.mimes' => 'Format foto harus JPEG atau PNG.',
            'photos.*.max' => 'Ukuran foto maksimal 5 MB.',
        ];
    }
}
