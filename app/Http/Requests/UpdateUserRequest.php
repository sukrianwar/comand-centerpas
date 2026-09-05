<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Parameter name di route adalah 'petuga', karena resource-nya 'petugas'
        // Tetapi secara default Laravel mungkin mendeteksinya sebagai 'petuga' karena pluralization.
        // Mari kita cek route param name-nya, biasanya 'petuga' atau kita definisikan explicit.
        // Kita bisa ambil dengan $this->route('petuga') atau get model langsung
        
        $user = $this->route('petuga') ?: $this->route('user'); // as fallback

        return [
            'name'     => ['required', 'string', 'max:150'],
            'username' => [
                'required', 
                'string', 
                'max:100', 
                Rule::unique('users', 'username')->ignore($user)
            ],
            'email'    => [
                'nullable', 
                'email', 
                'max:150', 
                Rule::unique('users', 'email')->ignore($user)
            ],
            'role_id'  => ['required', 'exists:roles,id'],
            'status'   => ['required', 'in:active,inactive'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique'    => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'email.unique'       => 'Email ini sudah digunakan oleh akun lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
