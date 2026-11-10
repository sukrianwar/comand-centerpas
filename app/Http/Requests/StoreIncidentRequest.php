<?php

namespace App\Http\Requests;

use App\Models\Incident;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Incident::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', Rule::in(array_keys(Incident::CATEGORIES))],
            'priority' => ['required', 'string', Rule::in(array_keys(Incident::PRIORITIES))],
            'area_id' => [
                'required',
                'integer',
                Rule::exists('areas', 'id')->whereNull('deleted_at'),
            ],
            'description' => ['required', 'string', 'max:2000'],
            'photos' => ['required', 'array', 'min:3', 'max:5'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul laporan wajib diisi.',
            'title.max' => 'Judul laporan maksimal 200 karakter.',
            'category.required' => 'Kategori insiden wajib dipilih.',
            'category.in' => 'Kategori insiden tidak valid.',
            'priority.required' => 'Prioritas insiden wajib dipilih.',
            'priority.in' => 'Prioritas insiden tidak valid.',
            'area_id.required' => 'Area insiden wajib dipilih.',
            'area_id.exists' => 'Area tidak ditemukan atau sudah dihapus.',
            'description.required' => 'Deskripsi insiden wajib diisi.',
            'description.max' => 'Deskripsi insiden maksimal 2.000 karakter.',
            'photos.required' => 'Foto bukti wajib diunggah.',
            'photos.array' => 'Foto bukti tidak valid.',
            'photos.min' => 'Foto bukti minimal 3 foto.',
            'photos.max' => 'Foto bukti maksimal 5 foto.',
            'photos.*.required' => 'Foto bukti wajib diunggah.',
            'photos.*.image' => 'Foto bukti harus berupa gambar.',
            'photos.*.mimes' => 'Format foto harus JPEG, PNG, atau WebP.',
            'photos.*.max' => 'Ukuran foto maksimal 5 MB.',
            'latitude.numeric' => 'Koordinat lintang tidak valid.',
            'latitude.between' => 'Koordinat lintang tidak valid.',
            'latitude.required_with' => 'Koordinat lintang dan bujur harus diisi bersamaan.',
            'longitude.numeric' => 'Koordinat bujur tidak valid.',
            'longitude.between' => 'Koordinat bujur tidak valid.',
            'longitude.required_with' => 'Koordinat lintang dan bujur harus diisi bersamaan.',
        ];
    }
}
