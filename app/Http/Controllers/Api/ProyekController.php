<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\KategoriSeni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ProyekController extends Controller
{
    public function index()
    {
        try {
            $proyeks = Proyek::whereNull('deleted_at')->get();

            if ($proyeks->count() > 0) {
                Log::info('Data Proyek Berhasil Ditampilkan');
                return response()->json([
                    'data' => $proyeks,
                    'status' => 'success',
                    'message' => 'Data Proyek Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Proyek Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Proyek Kosong',
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

    public function indexProyekUser(Request $request)
    {
        try {
            $seniman_id = $request->input('seniman_id');

            if (!$seniman_id) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Seniman ID tidak ditemukan'
                ], 400);
            }

            $proyeks = Proyek::where('seniman_id', $seniman_id)
                ->whereNull('deleted_at')
                ->get();

            if ($proyeks->count() > 0) {
                Log::info('Data Proyek untuk Seniman dengan ID ' . $seniman_id . ' Berhasil Ditampilkan');
                return response()->json([
                    'seniman_id' => $seniman_id,
                    'data' => $proyeks,
                    'status' => 'success',
                    'message' => 'Data Proyek untuk Seniman Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Proyek untuk Seniman dengan ID ' . $seniman_id . ' Kosong');
            return response()->json([
                'seniman_id' => $seniman_id,
                'data' => null,
                'status' => 'success',
                'message' => 'Data Proyek Kosong',
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

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'judul_proyek.required' => 'Judul proyek wajib diisi.',
                'judul_proyek.max' => 'Judul proyek tidak boleh lebih dari 100 karakter.',
                'deskripsi_proyek.required' => 'Deskripsi proyek wajib diisi.',
                'deskripsi_proyek.string' => 'Deskripsi proyek harus berupa teks.',
                'waktu_mulai.required' => 'Waktu mulai proyek wajib diisi.',
                'waktu_mulai.date' => 'Waktu mulai proyek harus berupa tanggal yang valid.',
                'waktu_selesai.required' => 'Waktu selesai proyek wajib diisi.',
                'waktu_selesai.date' => 'Waktu selesai proyek harus berupa tanggal yang valid.',
                'lokasi_proyek.required' => 'Lokasi proyek wajib diisi.',
                'lokasi_proyek.string' => 'Lokasi proyek harus berupa teks.',
                'tautan_proyek.required' => 'Tautan proyek wajib diisi.',
                'tautan_proyek.string' => 'Tautan proyek harus berupa teks.',
            ];

            $validate = Validator::make($storeData, [
                'seniman_id' => 'required|exists:seniman,id',
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'judul_proyek' => 'required|max:100',
                'deskripsi_proyek' => 'required|string',
                'waktu_mulai' => 'required|date',
                'waktu_selesai' => 'required|date',
                'lokasi_proyek' => 'required|string',
                'tautan_proyek' => 'required|string',
                'status_proyek' => 'required|boolean',
                'jumlah_like' => 'required|numeric',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $kategori = KategoriSeni::where('nama_kategori', $request->nama_kategori)->first();

            if (!$kategori) {
                Log::error('Kategori tidak ditemukan dengan nama_kategori: ' . $request->nama_kategori);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori Seni tidak ditemukan',
                ], 404);
            }

            $storeData['kategori_id'] = $kategori->id;

            $proyek = Proyek::create($storeData);

            Log::info('Data Proyek Berhasil Ditambahkan');
            return response()->json([
                'data' => $proyek,
                'status' => 'success',
                'message' => 'Data Proyek Berhasil Ditambahkan',
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
            $proyek = Proyek::whereNull('deleted_at')->find($id);

            if (!$proyek) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Proyek tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $proyek,
                'status' => 'success',
                'message' => 'Data Proyek Berhasil Ditampilkan',
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
            $proyek = Proyek::whereNull('deleted_at')->find($id);

            if (!$proyek) {
                Log::error('Data Proyek Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Proyek Tidak Ditemukan',
                ], 404);
            }

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'judul_proyek.required' => 'Judul proyek wajib diisi.',
                'judul_proyek.max' => 'Judul proyek tidak boleh lebih dari 100 karakter.',
                'deskripsi_proyek.required' => 'Deskripsi proyek wajib diisi.',
                'deskripsi_proyek.string' => 'Deskripsi proyek harus berupa teks.',
                'waktu_mulai.required' => 'Waktu mulai proyek wajib diisi.',
                'waktu_mulai.date' => 'Waktu mulai proyek harus berupa tanggal yang valid.',
                'waktu_selesai.required' => 'Waktu selesai proyek wajib diisi.',
                'waktu_selesai.date' => 'Waktu selesai proyek harus berupa tanggal yang valid.',
                'lokasi_proyek.required' => 'Lokasi proyek wajib diisi.',
                'lokasi_proyek.string' => 'Lokasi proyek harus berupa teks.',
                'tautan_proyek.required' => 'Tautan proyek wajib diisi.',
                'tautan_proyek.string' => 'Tautan proyek harus berupa teks.',
            ];
            $validate = Validator::make($request->all(), [
                'seniman_id' => 'required|exists:seniman,id',
                'kategori_id' => 'required|exists:kategori_senis,id',
                'judul_proyek' => 'required|string|max:100',
                'deskripsi_proyek' => 'required|string',
                'waktu_mulai' => 'required|date',
                'waktu_selesai' => 'required|date',
                'lokasi_proyek' => 'required|string',
                'tautan_proyek' => 'required|string',
                'status_proyek' => 'required|boolean',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $proyek->update($request->all());

            Log::info('Data Proyek Berhasil Diupdate');
            return response()->json([
                'data' => $proyek,
                'status' => 'success',
                'message' => 'Data Proyek Berhasil Diupdate',
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
            $proyek = Proyek::whereNull('deleted_at')->find($id);

            if (!$proyek) {
                Log::error('Data Proyek Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Proyek Tidak Ditemukan',
                ], 404);
            }

            if ($proyek->delete()) {
                Log::info('Data Proyek Berhasil Dihapus');
                return response()->json([
                    'data' => $proyek,
                    'status' => 'success',
                    'message' => 'Data Proyek Berhasil Dihapus',
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

    public function likeProyek($proyekId) {
        $proyek = Proyek::findOrFail($proyekId);

        $proyek->jumlah_like += 1;
        $proyek->save();

        return response()->json(['message' => 'Proyek liked successfully', 'jumlah_like' => $proyek->jumlah_like]);
    }

    public function unlikeProyek($proyekId) {
        $proyek = Proyek::findOrFail($proyekId);

        if ($proyek->jumlah_like > 0) {
            $proyek->jumlah_like -= 1;
            $proyek->save();
        }

        return response()->json(['message' => 'Proyek unliked successfully', 'jumlah_like' => $proyek->jumlah_like]);
    }


}
