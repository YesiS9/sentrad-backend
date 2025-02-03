<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penilai;
use App\Models\Rubrik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RubrikController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $rubriks = Rubrik::whereNull('rubriks.deleted_at')
                ->join('penilais', 'rubriks.penilai_id', '=', 'penilais.id')
                ->select('rubriks.*', 'penilais.nama_penilai')
                ->paginate($perPage);


            Log::info('Rubrik Data:', $rubriks->toArray());

            if ($rubriks->count() > 0) {
                Log::info('Data Rubrik Berhasil Ditampilkan');
                return response()->json([
                    'data' => $rubriks->items(),
                    'current_page' => $rubriks->currentPage(),
                    'per_page' => $rubriks->perPage(),
                    'total' => $rubriks->total(),
                    'last_page' => $rubriks->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Rubrik Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Rubrik Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Rubrik Kosong',
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


    public function indexByUser(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $userId = Auth::id();

            $penilaiIds = Penilai::where('user_id', $userId)->pluck('id');

            $rubriks = Rubrik::whereIn('penilai_id', $penilaiIds)
                ->whereNull('deleted_at')
                ->paginate($perPage);

            Log::info('Rubrik Data by User:', $rubriks->toArray());

            if ($rubriks->count() > 0) {
                Log::info('Data Rubrik by User Berhasil Ditampilkan');
                return response()->json([
                    'data' => $rubriks->items(),
                    'current_page' => $rubriks->currentPage(),
                    'per_page' => $rubriks->perPage(),
                    'total' => $rubriks->total(),
                    'last_page' => $rubriks->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Rubrik by User Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Rubrik by User Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Rubrik by User Kosong',
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
                'nama_rubrik.required' => 'Nama rubrik harus diisi.',
                'nama_rubrik.string' => 'Nama rubrik harus berupa teks.',
                'nama_rubrik.max' => 'Nama rubrik tidak boleh lebih dari 100 karakter.',
                'nama_rubrik.unique' => 'Nama rubrik sudah digunakan, silakan gunakan nama lain.',
                'deskripsi_rubrik.required' => 'Deskripsi rubrik harus diisi.',
                'deskripsi_rubrik.string' => 'Deskripsi rubrik harus berupa teks.',
                'bobot.required' => 'Bobot harus diisi.',
                'bobot.numeric' => 'Bobot harus berupa angka.',
                'bobot.gte' => 'Bobot tidak boleh kurang dari 0.',
                'bobot.lte' => 'Bobot tidak boleh lebih dari 20.',
            ];

            $validator = Validator::make($request->all(), [
                'nama_rubrik' => 'required|string|max:100|unique:rubriks',
                'deskripsi_rubrik' => 'required|string',
                'bobot' => 'required|numeric|gte:0|lte:20',
            ], $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            try {
                $rubrik = Rubrik::create([
                    'nama_rubrik' => $request->nama_rubrik,
                    'deskripsi_rubrik' => $request->deskripsi_rubrik,
                    'bobot' => $request->bobot,
                    'penilai_id' => $request->penilai_id,
                ]);

                Log::info('Rubrik added successfully', ['rubrik' => $rubrik]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Rubrik added successfully',
                    'data' => $rubrik,
                ], 200);
            } catch (\Exception $e) {
                Log::error('Error adding Rubrik: ' . $e->getMessage());

                return response()->json([
                    'status' => 'error',
                    'message' => 'Error adding Rubrik: ' . $e->getMessage(),
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error adding Rubrik: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $rubrik = Rubrik::whereNull('deleted_at')->find($id);

            if (!$rubrik) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Rubrik tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $rubrik,
                'status' => 'success',
                'message' => 'Data Rubrik Berhasil Ditampilkan',
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
            $rubrik = Rubrik::whereNull('deleted_at')->find($id);

            if (!$rubrik) {
                Log::error('Data Rubrik Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Rubrik Tidak Ditemukan',
                ], 404);
            }

            $messages = [
                'nama_rubrik.required' => 'Nama rubrik harus diisi.',
                'nama_rubrik.string' => 'Nama rubrik harus berupa teks.',
                'nama_rubrik.max' => 'Nama rubrik tidak boleh lebih dari 100 karakter.',
                'nama_rubrik.unique' => 'Nama rubrik sudah digunakan, silakan gunakan nama lain.',
                'deskripsi_rubrik.required' => 'Deskripsi rubrik harus diisi.',
                'deskripsi_rubrik.string' => 'Deskripsi rubrik harus berupa teks.',
                'bobot.required' => 'Bobot harus diisi.',
                'bobot.numeric' => 'Bobot harus berupa angka.',
                'bobot.gte' => 'Bobot tidak boleh kurang dari 0.',
                'bobot.lte' => 'Bobot tidak boleh lebih dari 20.',
            ];

            $validator = Validator::make($request->all(), [
                'nama_rubrik' => 'required|string|max:100',
                'deskripsi_rubrik' => 'required|string',
                'bobot' => 'required|numeric|gte:0|lte:20',
            ], $messages);

            if ($validator->fails()) {
                Log::error('Validation error: ' . $validator->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validator->errors(),
                ], 400);
            }

            $rubrik->nama_rubrik = $request->nama_rubrik;
            $rubrik->deskripsi_rubrik = $request->deskripsi_rubrik;
            $rubrik->bobot = $request->bobot;

            $rubrik->save();

            Log::info('Data Rubrik Berhasil Diupdate');
            return response()->json([
                'data' => $rubrik,
                'status' => 'success',
                'message' => 'Data Rubrik Berhasil Diupdate',
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
            $rubrik = Rubrik::whereNull('deleted_at')->find($id);

            if (!$rubrik) {
                Log::error('Data Rubrik Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Rubrik Tidak Ditemukan',
                ], 404);
            }

            if ($rubrik->delete()) {
                Log::info('Data Rubrik Berhasil Dihapus');
                return response()->json([
                    'data' => $rubrik,
                    'status' => 'success',
                    'message' => 'Data Rubrik Berhasil Dihapus',
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
