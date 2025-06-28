<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karya;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KaryaController extends Controller
{
    public function index($portofolio_id)
    {
        try {
            Log::info('Received portofolio_id: ' . $portofolio_id);

            if (!$portofolio_id) {
                Log::info('portofolio_id is required but not provided');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'portofolio_id is required'
                ], 400);
            }


            $karyas = Karya::whereNull('deleted_at')
                        ->where('portofolio_id', $portofolio_id)
                        ->get();

            if ($karyas->isNotEmpty()) {
                Log::info('Data Karya Berhasil Ditampilkan');
                return response()->json([
                    'data' => $karyas,
                    'status' => 'success',
                    'message' => 'Data Karya Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Karya Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Karya Kosong',
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
                'portofolio_id.required' => 'Nama Portofolio wajib diisi.',
                'judul_karya.required' => 'Judul karya wajib diisi.',
                'judul_karya.string' => 'Judul karya harus berupa teks.',
                'tgl_pembuatan.required' => 'Tanggal pembuatan wajib diisi.',
                'tgl_pembuatan.date_format' => 'Tanggal pembuatan harus menggunakan format d/m/Y.',
                'deskripsi_karya.required' => 'Deskripsi karya wajib diisi.',
                'deskripsi_karya.string' => 'Deskripsi karya harus berupa teks.',
                'bentuk_karya.required' => 'Bentuk karya wajib diisi.',
                'bentuk_karya.string' => 'Bentuk karya harus berupa teks.',
                'media_karya.*.required' => 'Media karya wajib diunggah.',
                'media_karya.*.file' => 'Media karya harus berupa file.',
                'media_karya.*.mimes' => 'Media karya harus berupa file dengan ekstensi jpg, jpeg, png, mp4, atau mov.',
                'media_karya.*.max' => 'Ukuran file media karya tidak boleh lebih dari 200MB.',
                'status_karya.required' => 'Status karya wajib diisi.',
            ];
            $validate = Validator::make($request->all(), [
                'portofolio_id' => 'required|exists:portofolios,id',
                'judul_karya' => 'required|string',
                'tgl_pembuatan' => 'required|date_format:d/m/Y',
                'deskripsi_karya' => 'required|string',
                'bentuk_karya' => 'required|string',
                'media_karya.*' => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:204800',
                'status_karya' => 'required|boolean',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $portofolio = Portofolio::find($request->portofolio_id);
            if (!$portofolio) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Portofolio not found',
                ], 404);
            }

            $filePaths = [];
            if ($request->hasFile('media_karya')) {
                foreach ($request->file('media_karya') as $file) {
                    if ($file->isValid()) {
                        $filePaths[] = $file->store('media_karya', 'public');
                    }
                }
            }

            $storeData = $request->except('media_karya');
            $storeData['tgl_pembuatan'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_pembuatan'])->format('Y-m-d');
            $storeData['portofolio_id'] = $portofolio->id;
            $storeData['media_karya'] = json_encode($filePaths);

            $karya = Karya::create($storeData);

            $this->updateJumlahKarya($portofolio->id);

            Log::info('Data Karya Berhasil Ditambahkan');
            return response()->json([
                'data' => $karya,
                'status' => 'success',
                'message' => 'Data Karya Berhasil Ditambahkan',
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
            $messages = [
                'portofolio_id.required' => 'Nama Portofolio wajib diisi.',
                'judul_karya.required' => 'Judul karya wajib diisi.',
                'judul_karya.string' => 'Judul karya harus berupa teks.',
                'tgl_pembuatan.required' => 'Tanggal pembuatan wajib diisi.',
                'tgl_pembuatan.date_format' => 'Tanggal pembuatan harus menggunakan format d/m/Y.',
                'deskripsi_karya.required' => 'Deskripsi karya wajib diisi.',
                'deskripsi_karya.string' => 'Deskripsi karya harus berupa teks.',
                'bentuk_karya.required' => 'Bentuk karya wajib diisi.',
                'bentuk_karya.string' => 'Bentuk karya harus berupa teks.',
                'media_karya.*.required' => 'Media karya wajib diunggah.',
                'media_karya.*.file' => 'Media karya harus berupa file.',
                'media_karya.*.mimes' => 'Media karya harus berupa file dengan ekstensi jpg, jpeg, png, mp4, atau mov.',
                'media_karya.*.max' => 'Ukuran file media karya tidak boleh lebih dari 200MB.',
                'status_karya.required' => 'Status karya wajib diisi.',
            ];
            $validate = Validator::make($request->all(), [
                'portofolio_id' => 'required|exists:portofolios,id',
                'judul_karya' => 'required|string',
                'tgl_pembuatan' => 'required|date_format:d/m/Y',
                'deskripsi_karya' => 'required|string',
                'bentuk_karya' => 'required|string',
                'media_karya.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:204800',
                'status_karya' => 'required|boolean',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $karya = Karya::find($id);

            if (!$karya) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Karya not found',
                ], 404);
            }

            $portofolio = Portofolio::find($request->portofolio_id);
            if (!$portofolio) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Portofolio not found',
                ], 404);
            }

            $filePaths = json_decode($karya->media_karya, true);
            if ($request->hasFile('media_karya')) {
                foreach ($request->file('media_karya') as $file) {
                    if ($file->isValid()) {
                        if ($filePaths) {
                            foreach ($filePaths as $oldFilePath) {
                                if (Storage::exists('public/' . $oldFilePath)) {
                                    Storage::delete('public/' . $oldFilePath);
                                }
                            }
                        }
                        $filePaths = [];
                        foreach ($request->file('media_karya') as $newFile) {
                            if ($newFile->isValid()) {
                                $filePaths[] = $newFile->store('media_karya', 'public');
                            }
                        }
                    }
                }
            }

            $updateData = $request->except('media_karya');
            $updateData['media_karya'] = json_encode($filePaths);
            $updateData['tgl_pembuatan'] = Carbon::createFromFormat('d/m/Y', $updateData['tgl_pembuatan'])->format('Y-m-d');
            $updateData['portofolio_id'] = $portofolio->id;

            $karya->update($updateData);

            $this->updateJumlahKarya($portofolio->id);

            Log::info('Data Karya Berhasil Diperbarui');
            return response()->json([
                'data' => $karya,
                'status' => 'success',
                'message' => 'Data Karya Berhasil Diperbarui',
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
            $karya = Karya::with('portofolio:id,judul_portofolio')->whereNull('deleted_at')->find($id);

            if (!$karya) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Karya tidak ditemukan',
                ], 404);
            }

            $judul_portofolio = $karya->portofolio ? $karya->portofolio->judul_portofolio : 'Portofolio tidak tersedia';

            $data = [
                'id' => $karya->id,
                'judul_karya' => $karya->judul_karya,
                'deskripsi_karya' => $karya->deskripsi_karya,
                'tgl_pembuatan' => $karya->tgl_pembuatan,
                'media_karya' => $karya->media_karya,
                'bentuk_karya' => $karya->bentuk_karya,
                'status_karya' => $karya->status_karya,
                'judul_portofolio' => $judul_portofolio,
            ];

            return response()->json([
                'data' => $data,
                'status' => 'success',
                'message' => 'Data Karya Berhasil Ditampilkan',
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
            $karya = Karya::whereNull('deleted_at')->find($id);

            if (!$karya) {
                Log::error('Data Karya Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Karya Tidak Ditemukan',
                ], 404);
            }

            $filePaths = json_decode($karya->media_karya, true);
            if ($filePaths) {
                foreach ($filePaths as $filePath) {
                    if (Storage::exists('public/' . $filePath)) {
                        Storage::delete('public/' . $filePath);
                    }
                }
            }

            $karya->delete();

            $portofolio = Portofolio::find($karya->portofolio_id);
            if ($portofolio) {
                $this->updateJumlahKarya($portofolio->id);
            }

            Log::info('Data Karya Berhasil Dihapus');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Karya Berhasil Dihapus',
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

    private function updateJumlahKarya($portofolioId)
    {
        $jumlahKarya = Karya::where('portofolio_id', $portofolioId)
            ->whereNull('deleted_at')
            ->count();

        Portofolio::find($portofolioId)->update(['jumlah_karya' => $jumlahKarya]);
    }
}

