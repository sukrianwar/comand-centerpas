<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('role')
            ->select('id', 'role_id', 'name', 'username', 'email', 'phone', 'status', 'created_at')
            ->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::select('id', 'role_name')->get();

        return view('petugas.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::select('id', 'role_name')->get();
        return view('petugas.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return redirect()
            ->route('petugas.index')
            ->with('success', "Akun petugas \"{$user->name}\" berhasil dibuat.");
    }

    public function edit(User $petuga): View
    {
        $this->authorize('update', $petuga);

        $roles = Role::select('id', 'role_name')->get();
        return view('petugas.edit', ['user' => $petuga, 'roles' => $roles]);
    }

    public function update(UpdateUserRequest $request, User $petuga): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $petuga->update($data);

        return redirect()
            ->route('petugas.index')
            ->with('success', "Data petugas \"{$petuga->name}\" berhasil diperbarui.");
    }

    public function destroy(User $petuga): RedirectResponse
    {
        $this->authorize('delete', $petuga);

        if (auth()->id() === $petuga->id) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $suffix = '#deleted-' . $petuga->id;

        $petuga->update([
            'username' => Str::limit($petuga->username, 100 - strlen($suffix), '') . $suffix,
            'email' => null,
        ]);

        $petuga->delete();

        return redirect()
            ->route('petugas.index')
            ->with('success', "Akun petugas berhasil dihapus.");
    }

    public function toggleStatus(User $petuga): JsonResponse
    {
        $this->authorize('toggleStatus', $petuga);
        // Cegah user menonaktifkan akunnya sendiri
        if (auth()->id() === $petuga->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
            ], 403);
        }

        $petuga->status = $petuga->status === 'active' ? 'inactive' : 'active';
        $petuga->save();

        return response()->json([
            'success' => true,
            'new_status' => $petuga->status,
            'message' => 'Status berhasil diubah.'
        ]);
    }
}
