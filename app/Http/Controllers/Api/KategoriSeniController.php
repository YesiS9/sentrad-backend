<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSeni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KategoriSeniController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->paginate($perPage);

            if ($kategoriSeni->count() > 0) {
                Log::info('Data Kategori Seni Berhasil Ditampilkan');
                return response()->json([
                    'data' => $kategoriSeni->items(),
                    'current_page' => $kategoriSeni->currentPage(),
                    'per_page' => $kategoriSeni->perPage(),
                    'total' => $kategoriSeni->total(),
                    'last_page' => $kategoriSeni->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Kategori Seni Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Kategori Seni Kosong');
            return response()->json([
                'data' => null,
                'meta' => null,
                'status' => 'success',
                'message' => 'Data Kategori Seni Kosong',
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

    public function indexKategori(Request $request){
        try {
            $kategori = KategoriSeni::whereNull('deleted_at')->get();

            if ($kategori->isNotEmpty()) {
                Log::info('Data Kategori Seni Berhasil Ditampilkan');
                return response()->json([
                    'data' => $kategori,
                    'status' => 'success',
                    'message' => 'Data Kategori Seni Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Kategori Seni Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Kategori Seni Kosong',
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
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.string' => 'Nama kategori harus berupa teks.',
                'nama_kategori.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
                'deskripsi_kategori.required' => 'Deskripsi kategori wajib diisi.',
            ];
            $validate = Validator::make($request->all(), [
                'nama_kategori' => 'required|string|max:100',
                'deskripsi_kategori' => 'required',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategoriSeni = KategoriSeni::create([
                'user_id' => auth()->id(),
                'nama_kategori' => $request->nama_kategori,
                'deskripsi_kategori' => $request->deskripsi_kategori,
            ]);

            Log::info('Data Kategori Seni Berhasil Ditambahkan');
            return response()->json([
                'data' => $kategoriSeni,
                'status' => 'success',
                'message' => 'Data Kategori Seni Berhasil Ditambahkan',
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
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->find($id);

            if (!$kategoriSeni) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Kategori Seni tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $kategoriSeni,
                'status' => 'success',
                'message' => 'Data Kategori Seni Berhasil Ditampilkan',
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
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->find($id);

            if (!$kategoriSeni) {
                Log::error('Data Kategori Seni Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Kategori Seni Tidak Ditemukan',
                ], 404);
            }

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.string' => 'Nama kategori harus berupa teks.',
                'nama_kategori.max' => 'Nama kategori tidak boleh lebih dari 100 karakter.',
                'deskripsi_kategori.required' => 'Deskripsi kategori wajib diisi.',
            ];

            $validate = Validator::make($request->all(), [
                'user_id' => 'required',
                'nama_kategori' => 'required|string|max:100',
                'deskripsi_kategori' => 'required',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategoriSeni->user_id = $request->user_id;
            $kategoriSeni->nama_kategori = $request->nama_kategori;
            $kategoriSeni->deskripsi_kategori = $request->deskripsi_kategori;

            $kategoriSeni->save();

            Log::info('Data Kategori Seni Berhasil Diupdate');
            return response()->json([
                'data' => $kategoriSeni,
                'status' => 'success',
                'message' => 'Data Kategori Seni Berhasil Diupdate',
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
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')->find($id);

            if (!$kategoriSeni) {
                Log::error('Data Kategori Seni Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Kategori Seni Tidak Ditemukan',
                ], 404);
            }

            if ($kategoriSeni->delete()) {
                Log::info('Data Kategori Seni Berhasil Dihapus');
                return response()->json([
                    'data' => $kategoriSeni,
                    'status' => 'success',
                    'message' => 'Data Kategori Seni Berhasil Dihapus',
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
