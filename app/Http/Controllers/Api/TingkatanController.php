<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tingkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TingkatanController extends Controller
{
    public function index(Request $request){
        try {
            $perPage = $request->input('per_page', 10);
            $tingkatan = Tingkatan::whereNull('deleted_at')->paginate($perPage);

            if (count($tingkatan) > 0) {
                Log::info('Data Tingkatan Berhasil Ditampilkan');
                return response()->json([
                    'data' => $tingkatan->items(),
                    'current_page' => $tingkatan->currentPage(),
                    'per_page' => $tingkatan->perPage(),
                    'total' => $tingkatan->total(),
                    'last_page' => $tingkatan->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Tingkatan Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Tingkatan Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Tingkatan Kosong',
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

    public function store(Request $request){
        try {

            $storeData = $request->all();

            $messages = [
                'nama_tingkatan.required' => 'Nama tingkatan wajib diisi.',
                'deskripsi_tingkatan.required' => 'Deskripsi tingkatan wajib diisi.',
                'nilai_min.required' => 'Nilai minimum wajib diisi.',
                'nilai_min.numeric' => 'Nilai minimum harus berupa angka.',
                'nilai_max.required' => 'Nilai maksimum wajib diisi.',
                'nilai_max.numeric' => 'Nilai maksimum harus berupa angka.',
            ];

            $validate = Validator::make($storeData, [
                'nama_tingkatan' => 'required',
                'deskripsi_tingkatan' => 'required',
                'nilai_min' => 'required|numeric',
                'nilai_max' => 'required|numeric',
            ], $messages);


            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $tingkatan = Tingkatan::create($storeData);

            Log::info('Data Tingkatan Berhasil Ditambahakan');
            return response()->json([
                'data' => $tingkatan,
                'status' => 'success',
                'message' => 'Data Tingkatan Berhasil Ditambahakan',
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
            $tingkatan = Tingkatan::whereNull('deleted_at')->find($id);

            if (!$tingkatan) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Tingkatan tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $tingkatan,
                'status' => 'success',
                'message' => 'Data Tingkatan Berhasil Ditampilkan',
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
            $tingkatan = Tingkatan::whereNull('deleted_at')->find($id);

            if (!$tingkatan) {
                Log::error('Data Tingkatan Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Tingkatan Tidak Ditemukan',
                ], 404);
            }

            $messages = [
                'nama_tingkatan.required' => 'Nama tingkatan wajib diisi.',
                'deskripsi_tingkatan.required' => 'Deskripsi tingkatan wajib diisi.',
                'nilai_min.required' => 'Nilai minimum wajib diisi.',
                'nilai_min.numeric' => 'Nilai minimum harus berupa angka.',
                'nilai_max.required' => 'Nilai maksimum wajib diisi.',
                'nilai_max.numeric' => 'Nilai maksimum harus berupa angka.',
            ];

            $validate = Validator::make($request->all(), [
                'nama_tingkatan' => 'required',
                'deskripsi_tingkatan' => 'required',
                'nilai_min' => 'required|numeric',
                'nilai_max' => 'required|numeric',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $tingkatan->nama_tingkatan = $request->nama_tingkatan;
            $tingkatan->deskripsi_tingkatan = $request->deskripsi_tingkatan;
            $tingkatan->nilai_min = $request->nilai_min;
            $tingkatan->nilai_max = $request->nilai_max;

            $tingkatan->save();

            Log::info('Data Tingkatan Berhasil Diupdate');
            return response()->json([
                'data' => $tingkatan,
                'status' => 'success',
                'message' => 'Data Tingkatan Berhasil Diupdate',
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
            $tingkatan = Tingkatan::whereNull('deleted_at')->find($id);

            if (!$tingkatan) {
                Log::error('Data Tingkatan Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Tingkatan Tidak Ditemukan',
                ], 404);
            }

            if ($tingkatan->delete()) {
                Log::info('Data Tingkatan Berhasil Dihapus');
                return response()->json([
                    'data' => $tingkatan,
                    'status' => 'success',
                    'message' => 'Data Tingkatan Berhasil Dihapus',
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
