<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSeni;
use App\Models\Seni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SeniController extends Controller
{
    public function index(Request $request){
        try {
            $perPage = $request->input('per_page', 10);
            $seni = Seni::whereNull('deleted_at')->paginate($perPage);

            if (count($seni) > 0) {
                Log::info('Data Seni Berhasil Ditampilkan');
                return response()->json([
                    'data' => $seni->items(),
                    'current_page' => $seni->currentPage(),
                    'per_page' => $seni->perPage(),
                    'total' => $seni->total(),
                    'last_page' => $seni->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Seni Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Penilai Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Penilai Kosong',
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

    public function getSeniByKategori($kategoriNama)
    {
        try {
            $kategori = KategoriSeni::where('nama_kategori', $kategoriNama)->first();

            if (!$kategori) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }

            $seni = Seni::where('kategori_id', $kategori->id)->get();

            return response()->json([
                'status' => 'success',
                'data' => $seni
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data seni: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request){
        try {

            $storeData = $request->all();

            $masseges = [
                'nama_kategori.required' => 'Nama kategori harus diisi.',
                'nama_seni.required' => 'Nama seni harus diisi.',
                'nama_seni.unique' => 'Nama seni sudah digunakan, silakan gunakan nama lain.',
                'deskripsi_seni.required' => 'Deskripsi seni harus diisi.',
                'deskripsi_seni.string' => 'Deskripsi seni harus berupa teks.',
                'status_seni.required' => 'Status seni harus diisi.',
            ];
            $validate = Validator::make($storeData, [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama_seni' => 'required', Rule::unique('senis', 'nama_seni')->whereNull('deleted_at'),
                'deskripsi_seni' => 'required',
                'status_seni' => 'required|in:Budaya,Non-budaya,Modern'
            ]);


            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategori = KategoriSeni::where('nama_kategori', $storeData['nama_kategori'])->first();
            if (!$kategori) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }
            $storeData['kategori_id'] = $kategori->id;
            $seni = Seni::create($storeData);

            Log::info('Data Seni Berhasil Ditambahakan');
            return response()->json([
                'data' => $seni,
                'status' => 'success',
                'message' => 'Data Seni Berhasil Ditambahakan',
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


    public function show($id){
        try {
            $seni = Seni::whereNull('deleted_at')->find($id);

            if (!$seni) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Seni tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $seni,
                'status' => 'success',
                'message' => 'Data Seni Berhasil Ditampilkan',
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


    public function update(Request $request, $id){
        try {
            $seni = Seni::whereNull('deleted_at')->find($id);

            if (!$seni) {
                Log::error('Data Seni Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Seni Tidak Ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama_seni' => 'required',
                'deskripsi_seni' => 'required',
                'status_seni' => 'required'
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategori = KategoriSeni::where('nama_kategori',  $request->nama_kategori)->first();
            if (!$kategori) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }

            $seni->kategori_id = $request->kategori_id;
            $seni->nama_seni = $request->nama_seni;
            $seni->deskripsi_seni = $request->deskripsi_seni;
            $seni->status_seni = $request->status_seni;

            $seni->save();

            Log::info('Data Seni Berhasil Diupdate');
            return response()->json([
                'data' => $seni,
                'status' => 'success',
                'message' => 'Data Seni Berhasil Diupdate',
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


    public function destroy($id){
        try {
            $seni = Seni::whereNull('deleted_at')->find($id);

            if (!$seni) {
                Log::error('Data Seni Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Seni Tidak Ditemukan',
                ], 404);
            }

            if ($seni->delete()) {
                Log::info('Data Seni Berhasil Dihapus');
                return response()->json([
                    'data' => $seni,
                    'status' => 'success',
                    'message' => 'Data Seni Berhasil Dihapus',
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
