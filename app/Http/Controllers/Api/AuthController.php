<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penilai;
use App\Models\Role;
use App\Models\Seniman;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller{
    public function hash(Request $request){
        echo Hash::make($request->query('txt'));
    }
    public function register(Request $request)
    {
        try {
            $messages = [
                'username.required' => 'Username harus diisi.',
                'username.string' => 'Username harus berupa teks.',
                'username.max' => 'Username maksimal 255 karakter.',
                'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
                'email.required' => 'Email harus diisi.',
                'email.string' => 'Email harus berupa teks.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 255 karakter.',
                'email.unique' => 'Email sudah digunakan, silakan gunakan email lain.',
                'password.required' => 'Password harus diisi.',
                'password.string' => 'Password harus berupa teks.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'foto.file' => 'Foto harus berupa file.',
                'foto.image' => 'File yang diunggah harus berupa gambar.',
                'foto.max' => 'Ukuran foto maksimal adalah 200 MB.',
            ];

            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:204800',
            ], $messages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            try {
                $role = Role::where('nama_role', 'seniman')->firstOrFail();

                $fotoPath = 'profil_user/user.jpg';
                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    if ($file->isValid()) {
                        $fotoPath = $file->store('profil_user', 'public');
                    }
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

                event(new Registered($user));
                $user->sendEmailVerificationNotification();

                return response()->json([
                    'status' => 'success',
                    'message' => 'User registered successfully. Please check your email for verification.',
                    'data' => $user,
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error during registration: ' . $e->getMessage(),
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error during registration: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.exists' => 'Email tidak ditemukan.',
            'password.required' => 'Password wajib diisi.'
        ]);

        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                Log::error('Email atau password salah');
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Email atau password salah'
                ], 401);
            }

            $user = Auth::user();

            $tokenResult = $user->createToken('AuthToken');
            $token = $tokenResult->accessToken;

            $getRole = UserRole::join('roles', 'user_roles.role_id', '=', 'roles.id')
                ->where('user_roles.user_id', $user->id)
                ->select('roles.nama_role')
                ->first();

            $seniman_id = null;
            $penilai_id = null;

            if ($getRole) {
                $roleName = strtolower($getRole->nama_role);

                if ($roleName === 'seniman') {
                    $seniman = Seniman::where('user_id', $user->id)->first();
                    if ($seniman) {
                        $seniman_id = $seniman->id;
                    }
                } elseif ($roleName === 'penilai') {
                    $penilai = Penilai::where('user_id', $user->id)->first();
                    if ($penilai) {
                        $penilai_id = $penilai->id;
                    }
                }
            }

            Log::info('Login berhasil');
            return response()->json([
                'data' => [
                    'user' => $user,
                    'role' => $getRole,
                    'token' => $token,
                    'seniman_id' => $seniman_id,
                    'penilai_id' => $penilai_id
                ],
                'status' => 'success',
                'message' => 'Login berhasil'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function verifyEmail($id)
    {
        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
