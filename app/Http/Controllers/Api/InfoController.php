<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Info;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InfoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $info = Info::whereNull('deleted_at')->paginate($perPage);

            if ($info->isNotEmpty()) {
                Log::info('Data Info Berhasil Ditampilkan');
                return response()->json([
                    'data' => $info->items(),
                    'current_page' => $info->currentPage(),
                    'per_page' => $info->perPage(),
                    'total' => $info->total(),
                    'last_page' => $info->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Info Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Info Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Info Kosong',
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
            $messages = [
                'info.required' => 'Info wajib diisi.',
                'info.string' => 'Info harus berupa teks.',
            ];
            $validate = Validator::make($request->all(), [
                'info' => 'required|string',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $info = Info::create($request->all());

            Log::info('Data Info Berhasil Ditambahkan');
            return response()->json([
                'data' => $info,
                'status' => 'success',
                'message' => 'Data Info Berhasil Ditambahkan',
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
            $info = Info::whereNull('deleted_at')->find($id);

            if (!$info) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Info tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $info,
                'status' => 'success',
                'message' => 'Data Info Berhasil Ditampilkan',
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
            $info = Info::whereNull('deleted_at')->find($id);

            if (!$info) {
                Log::error('Data Info Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Info Tidak Ditemukan',
                ], 404);
            }

            $messages = [
                'info.required' => 'Info wajib diisi.',
                'info.string' => 'Info harus berupa teks.',
            ];
            $validate = Validator::make($request->all(), [
                'info' => 'required|string',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $info->update($request->all());

            Log::info('Data Info Berhasil Diupdate');
            return response()->json([
                'data' => $info,
                'status' => 'success',
                'message' => 'Data Info Berhasil Diupdate',
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
            $info = Info::whereNull('deleted_at')->find($id);

            if (!$info) {
                Log::error('Data Info Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Info Tidak Ditemukan',
                ], 404);
            }

            $info->delete();

            Log::info('Data Info Berhasil Dihapus');
            return response()->json([
                'data' => $info,
                'status' => 'success',
                'message' => 'Data Info Berhasil Dihapus',
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
}
