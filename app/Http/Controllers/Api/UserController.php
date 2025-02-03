<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $users = User::with('roles')
                         ->whereNull('deleted_at')
                         ->paginate($perPage);

            Log::info('Users:', $users->toArray());
            if ($users->count() > 0) {
                Log::info('Data User Berhasil Ditampilkan');
                return response()->json([
                    'data' => $users->items(),
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                    'status' => 'success',
                    'message' => 'Data User Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data User Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data User Kosong',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function indexByPenilai()
    {
        try {
            $role = Role::where('nama_role', 'Penilai')->first();

            if (!$role) {
                Log::info('Role Penilai tidak ditemukan');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role Penilai tidak ditemukan'
                ], 404);
            }

            $usernames = User::whereHas('roles', function ($query) use ($role) {
                $query->where('role_id', $role->id);
            })
            ->whereNull('deleted_at')
            ->whereDoesntHave('penilai')
            ->pluck('username');

            Log::info('Users with role Penilai:', $usernames->toArray());
            return response()->json([
                'data' => $usernames,
                'status' => 'success',
                'message' => 'Data User dengan role Penilai Berhasil Ditampilkan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function indexBySeniman()
    {
        try {
            $role = Role::where('nama_role', 'Seniman')->first();

            if (!$role) {
                Log::info('Role Seniman tidak ditemukan');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role Seniman tidak ditemukan'
                ], 404);
            }

            $usernames = User::whereHas('roles', function ($query) use ($role) {
                $query->where('role_id', $role->id);
            })
            ->whereNull('deleted_at')
            ->whereDoesntHave('seniman')
            ->pluck('username');

            Log::info('Usernames with role Seniman and not in seniman table:', $usernames->toArray());

            return response()->json([
                'data' => $usernames,
                'status' => 'success',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function storeByAdmin(Request $request)
    {
        try {
            $messages = [
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username telah dipakai.',
                'email.required' => 'Email wajib diisi.',
                'email.unique' => 'Email telah dipakai.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal harus terdiri dari 8 karakter.',
                'nama_role.required' => 'Role wajib dipilih.',
                'nama_role.exists' => 'Role yang dipilih tidak valid.',
                'foto.image' => 'Foto harus berupa file gambar.',
                'foto.max' => 'Ukuran foto tidak boleh lebih dari 200MB.',
            ];
            $validator = Validator::make($request->all(), [
                'username' => ['required','string','max:255',Rule::unique('users')->whereNull('deleted_at')],
                'email' => ['required','string','email','max:255', Rule::unique('users')->whereNull('deleted_at')],
                'password' => 'required|string|min:8',
                'nama_role' => 'required|string|exists:roles,nama_role',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:204800',
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 422);
            }

            $role = Role::where('nama_role', $request->nama_role)->firstOrFail();

            $fotoPath = 'profil_user/user.jpg';
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                if ($file->isValid()) {
                    $fotoPath = $file->store('profil_user', 'public');
                }
            }

            $storeData = $request->except('foto');
            $storeData['password'] = Hash::make($request->password);
            $storeData['email_verified_at'] = now();
            $storeData['foto'] = $fotoPath;

            $user = User::create($storeData);

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);

            Log::info('User added successfully', ['user' => $user]);

            return response()->json([
                'status' => 'success',
                'message' => 'User added successfully',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error adding user: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error adding user: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function store(Request $request)
    {
        try {
            $messages = [
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username telah dipakai.',
                'email.required' => 'Email wajib diisi.',
                'email.unique' => 'Email telah dipakai.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal harus terdiri dari 8 karakter.',
                'nama_role.required' => 'Role wajib dipilih.',
                'nama_role.exists' => 'Role yang dipilih tidak valid.',
                'foto.image' => 'Foto harus berupa file gambar.',
                'foto.max' => 'Ukuran foto tidak boleh lebih dari 200MB.',
            ];
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'nama_role' => 'required|string|exists:roles,nama_role',
                'foto' => 'nullable|file|image|max:204800',
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            $role = Role::where('nama_role', $request->nama_role)->firstOrFail();

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    if ($file->isValid()) {
                        $fotoPath = $file->store('profil_user', 'public');
                    }
                }
            }

            if (is_null($fotoPath)) {
                $fotoPath = 'profil_user/user.jpg';
            }

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'foto' => $fotoPath,
            ]);

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
            ]);

            Log::info('User added successfully', ['user' => $user]);

            return response()->json([
                'status' => 'success',
                'message' => 'User added successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error adding user: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Error adding user: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            $user = User::whereNull('deleted_at')->find($id);
            if (!$user) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data User Tidak Ditemukan',
                ], 404);
            }

            $messages = [
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username telah dipakai.',
                'email.required' => 'Email wajib diisi.',
                'email.unique' => 'Email telah dipakai.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal harus terdiri dari 8 karakter.',
                'nama_role.required' => 'Role wajib dipilih.',
                'nama_role.exists' => 'Role yang dipilih tidak valid.',
                'foto.image' => 'Foto harus berupa file gambar.',
                'foto.max' => 'Ukuran foto tidak boleh lebih dari 200MB.',
            ];

            $validate = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users,username,' . $id,
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|nullable|string|min:8',
                'nama_role' => 'required|string|max:255',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:204800',
            ], $messages);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validate->errors()->first(),
                ], 422);
            }

            $user->username = $request->username;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $role = Role::where('nama_role', $request->nama_role)->first();
            if ($role) {
                UserRole::where('user_id', $user->id)->delete();
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                ]);
            }

            if ($request->hasFile('foto')) {
                if ($request->file('foto')->isValid()) {
                    if ($user->foto && $user->foto !== 'profil_user/user.jpg') {
                        Storage::disk('public')->delete($user->foto);
                    }

                    $fotoPath = $request->file('foto')->store('profil_user', 'public');
                    $user->foto = $fotoPath;
                }
            } else {
                if (is_null($user->foto) || $user->foto === 'profil_user/user.jpg') {
                    $user->foto = 'profil_user/user.jpg';
                }
            }


            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data User berhasil diperbarui',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data user: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function show($id){
        try {
            $user = User::whereNull('deleted_at')->find($id);

            if (!$user) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data User tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $user,
                'status' => 'success',
                'message' => 'Data User Berhasil Ditampilkan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                Log::info('User tidak ditemukan');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $user->delete();

            Log::info('User berhasil dihapus');
            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTotalUser()
    {
        $totalUser = User::whereNull('deleted_at')->count();

        return response()->json(['total_users' => $totalUser]);
    }



}
