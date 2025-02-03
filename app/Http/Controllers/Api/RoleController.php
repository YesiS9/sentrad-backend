<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::whereNull('deleted_at')->get();

            return response()->json([
                'data' => $roles,
                'status' => 'success',
                'message' => 'Data Role Berhasil Ditampilkan',
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

    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'nama_role' => 'required|string|max:100',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $role = Role::create($storeData);

            Log::info('Data Role Berhasil Ditambahkan');
            return response()->json([
                'data' => $role,
                'status' => 'success',
                'message' => 'Data Role Berhasil Ditambahkan',
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

    public function show($id)
    {
        try {
            $role = Role::whereNull('deleted_at')->find($id);

            if (!$role) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Role tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $role,
                'status' => 'success',
                'message' => 'Data Role Berhasil Ditampilkan',
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

    public function update(Request $request, $id)
    {
        try {
            $role = Role::whereNull('deleted_at')->find($id);

            if (!$role) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Role Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'nama_role' => 'required|string|max:100',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $role->update($request->all());

            Log::info('Data Role Berhasil Diupdate');
            return response()->json([
                'data' => $role,
                'status' => 'success',
                'message' => 'Data Role Berhasil Diupdate',
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

    public function destroy($id)
    {
        try {
            $role = Role::whereNull('deleted_at')->find($id);

            if (!$role) {
                Log::error('Data Role Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Role Tidak Ditemukan',
                ], 404);
            }

            if ($role->delete()) {
                Log::info('Data Role Berhasil Dihapus');
                return response()->json([
                    'data' => $role,
                    'status' => 'success',
                    'message' => 'Data Role Berhasil Dihapus',
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
