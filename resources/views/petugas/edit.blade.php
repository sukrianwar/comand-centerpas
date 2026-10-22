<x-app-layout>

{{-- ══ Header ════════════════════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row md:items-end justify-between mb-lg gap-md">
    <div>
        <p class="text-body-md text-secondary mb-xs">
            <a href="{{ route('petugas.index') }}" class="hover:text-primary transition-colors">Data Petugas</a>
            <span class="mx-1 opacity-40">/</span>
            <span>Edit Petugas</span>
        </p>
        <h2 class="text-headline-lg font-headline-lg text-on-surface">Edit Data Petugas</h2>
    </div>
</div>

{{-- ══ Form ════════════════════════════════════════════════════════════════ --}}
<form method="POST" action="{{ route('petugas.update', $user) }}">
    @csrf
    @method('PUT')

    <div class="max-w-3xl soft-card p-lg">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
            
            {{-- Nama --}}
            <div class="md:col-span-2">
                <label for="name" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $user->name) }}"
                       placeholder="Contoh: John Doe"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                              @error('name') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('name')" class="mt-xs" />
            </div>

            {{-- Username --}}
            <div>
                <label for="username" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="username" name="username"
                       value="{{ old('username', $user->username) }}"
                       placeholder="Contoh: john_doe"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all font-mono
                              @error('username') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('username')" class="mt-xs" />
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Email <span class="text-secondary font-normal">(opsional)</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       placeholder="Contoh: john@example.com"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                              @error('email') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('email')" class="mt-xs" />
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label for="phone" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Nomor Telepon <span class="text-secondary font-normal">(opsional)</span>
                </label>
                <input type="text" id="phone" name="phone"
                       value="{{ old('phone', $user->phone) }}"
                       placeholder="Contoh: 0812xxxxxxx"
                       class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                              @error('phone') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                              @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                <x-input-error :messages="$errors->get('phone')" class="mt-xs" />
            </div>

            {{-- Role --}}
            <div>
                <label for="role_id" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Role (Peran) <span class="text-red-500">*</span>
                </label>
                <select id="role_id" name="role_id"
                        class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                               @error('role_id') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                               @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->role_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('role_id')" class="mt-xs" />
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Status Akses <span class="text-red-500">*</span>
                </label>
                <select id="status" name="status"
                        class="w-full bg-surface-container-low border rounded-xl py-xs px-sm text-body-sm text-on-surface outline-none transition-all
                               @error('status') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                               @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-xs" />
            </div>

            <div class="md:col-span-2 border-t border-outline-variant my-xs"></div>

            <div class="md:col-span-2">
                <p class="text-label-md text-secondary mb-2">
                    <span class="material-symbols-outlined text-[14px] align-middle">lock</span> 
                    <strong>Reset Password</strong> — Kosongkan kedua kolom di bawah jika tidak ingin mengubah password saat ini.
                </p>
            </div>

            {{-- Password dengan Alpine Show/Hide --}}
            <div x-data="{ show: false }">
                <label for="password" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Password Baru
                </label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="password" name="password"
                           class="w-full bg-surface-container-low border rounded-xl py-xs pl-sm pr-10 text-body-sm text-on-surface outline-none transition-all
                                  @error('password') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                  @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <button type="button" @click="show = !show" tabindex="-1" class="absolute right-2 top-1/2 -translate-y-1/2 text-secondary hover:text-primary">
                        <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-xs" />
            </div>

            {{-- Confirm Password --}}
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="block text-body-sm font-semibold text-on-surface mb-xs">
                    Konfirmasi Password Baru
                </label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                           class="w-full bg-surface-container-low border rounded-xl py-xs pl-sm pr-10 text-body-sm text-on-surface outline-none transition-all
                                  @error('password_confirmation') border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-200
                                  @else border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/10 @enderror">
                    <button type="button" @click="show = !show" tabindex="-1" class="absolute right-2 top-1/2 -translate-y-1/2 text-secondary hover:text-primary">
                        <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'">visibility</span>
                    </button>
                </div>
            </div>

        </div>

        {{-- Tombol Aksi --}}
        <div class="mt-lg pt-md border-t border-outline-variant flex gap-sm justify-end">
            <a href="{{ route('petugas.index') }}"
               class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-medium border border-outline-variant text-secondary hover:bg-surface-container transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="flex items-center gap-sm py-sm px-md rounded-xl text-body-sm font-bold bg-primary text-on-primary hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Update Petugas
            </button>
        </div>

    </div>
</form>

</x-app-layout>
