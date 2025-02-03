<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penilai;
use App\Models\KategoriSeni;
use App\Models\User;
use App\Models\KuotaPenilai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;

class PenilaiController extends Controller
{
    public function index(Request $request){
        try {
            $perPage = $request->input('per_page', 10);
            $penilai = Penilai::whereNull('deleted_at')->paginate($perPage);

            if (count($penilai) > 0) {
                Log::info('Data Penilai Berhasil Ditampilkan');
                return response()->json([
                    'data' => $penilai->items(),
                    'current_page' => $penilai->currentPage(),
                    'per_page' => $penilai->perPage(),
                    'total' => $penilai->total(),
                    'last_page' => $penilai->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Penilai Berhasil Ditampilkan',
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



    public function store(Request $request)
    {
        try {
            $storeData = $request->all();
            $messages = [
                'username.required' => 'Username wajib diisi.',
                'nama_penilai.required' => 'Nama penilai wajib diisi.',
                'nama_penilai.string' => 'Nama penilai harus berupa teks.',
                'nama_penilai.max' => 'Nama penilai tidak boleh lebih dari 100 karakter.',
                'alamat_penilai.required' => 'Alamat penilai wajib diisi.',
                'alamat_penilai.string' => 'Alamat penilai harus berupa teks.',
                'noTelp_penilai.required' => 'Nomor telepon penilai wajib diisi.',
                'noTelp_penilai.regex' => 'Nomor telepon harus diawali dengan 08 dan memiliki panjang antara 10 hingga 14 digit.',
                'nama_seni.required' => 'Nama seni wajib diisi.',
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'lembaga.required' => 'Lembaga wajib diisi.',
                'lembaga.string' => 'Lembaga harus berupa teks.',
                'lembaga.max' => 'Lembaga tidak boleh lebih dari 100 karakter.',
                'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
                'tgl_lahir.date_format' => 'Tanggal lahir harus dalam format d/m/Y.',
                'status_penilai.required' => 'Status penilai wajib diisi.',
                'status_penilai.string' => 'Status penilai harus berupa teks.',
                'status_penilai.in' => 'Status penilai harus salah satu dari: Aktif atau Nonaktif.',
                'kuota.required' => 'Kuota wajib diisi.',
                'kuota.numeric' => 'Kuota harus berupa angka.',
            ];

            $validate = Validator::make($storeData, [
                'username' => 'required|exists:users,username',
                'nama_penilai' => 'required|string|max:100',
                'alamat_penilai' => 'required|string',
                'noTelp_penilai' => 'required|regex:/^08\d{8,12}$/',
                'nama_seni' => 'required|exists:senis,nama_seni',
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'lembaga' => 'required|string|max:100',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'status_penilai' => 'required|string|in:Aktif,Nonaktif',
                'kuota' => 'required|numeric',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors()->first(),
                ], 400);
            }

            $user = User::where('username', $storeData['username'])->first();
            $kategori = KategoriSeni::where('nama_kategori', $storeData['nama_kategori'])->first();

            $existingPenilai = Penilai::where('user_id', $user->id)->first();
            if ($existingPenilai) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'User already has a Penilai entry',
                ], 400);
            }

            $storeData['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_lahir'])->format('Y-m-d');
            $storeData['user_id'] = $user->id;
            $storeData['kategori_id'] = $kategori->id;
            $storeData['bidang_ahli'] = $storeData['nama_seni'];
            unset($storeData['username']);
            unset($storeData['nama_kategori']);

            $penilai = Penilai::create($storeData);

            $currentMonth = Carbon::now()->startOfMonth();
            KuotaPenilai::create([
                'penilai_id' => $penilai->id,
                'periode_bulan' => $currentMonth,
                'kuota_terpakai' => 0,
            ]);

            Log::info('Data Penilai Berhasil Ditambahkan');
            return response()->json([
                'data' => $penilai,
                'status' => 'success',
                'message' => 'Data Penilai Berhasil Ditambahkan dan Kuota Penilaian Ditambahkan',
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
            $penilai = Penilai::whereNull('deleted_at')->find($id);

            if (!$penilai) {
                Log::error('Data Penilai Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilai Tidak Ditemukan',
                ], 404);
            }

            $messages = [
                'username.required' => 'Username wajib diisi.',
                'nama_penilai.required' => 'Nama penilai wajib diisi.',
                'nama_penilai.string' => 'Nama penilai harus berupa teks.',
                'nama_penilai.max' => 'Nama penilai tidak boleh lebih dari 100 karakter.',
                'alamat_penilai.required' => 'Alamat penilai wajib diisi.',
                'alamat_penilai.string' => 'Alamat penilai harus berupa teks.',
                'noTelp_penilai.required' => 'Nomor telepon penilai wajib diisi.',
                'noTelp_penilai.regex' => 'Nomor telepon harus diawali dengan 08 dan memiliki panjang antara 10 hingga 14 digit.',
                'nama_seni.required' => 'Nama seni wajib diisi.',
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'lembaga.required' => 'Lembaga wajib diisi.',
                'lembaga.string' => 'Lembaga harus berupa teks.',
                'lembaga.max' => 'Lembaga tidak boleh lebih dari 100 karakter.',
                'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
                'tgl_lahir.date_format' => 'Tanggal lahir harus dalam format d/m/Y.',
                'status_penilai.required' => 'Status penilai wajib diisi.',
                'status_penilai.string' => 'Status penilai harus berupa teks.',
                'status_penilai.in' => 'Status penilai harus salah satu dari: Aktif atau Nonaktif.',
                'kuota.required' => 'Kuota wajib diisi.',
                'kuota.numeric' => 'Kuota harus berupa angka.',
            ];

            $validate = Validator::make($request->all(), [
                'username' => 'required|exists:users,username',
                'nama_penilai' => 'required|string|max:100',
                'alamat_penilai' => 'required|string',
                'noTelp_penilai' => 'required|regex:/^08\d{8,12}$/',
                'nama_seni' => 'required|exists:senis,nama_seni',
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'lembaga' => 'required|string|max:100',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'status_penilai' => 'required|string|in:Aktif,Nonaktif',
                'kuota' => 'required|numeric',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors()->first(),
                ], 400);
            }

            $user = User::where('username', $request->username)->first();
            $kategori = KategoriSeni::where('nama_kategori', $request->nama_kategori)->first();

            try {
                $tgl_lahir = Carbon::createFromFormat('d/m/Y', $request->tgl_lahir)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Invalid date format for tgl_lahir',
                ], 400);
            }

            $penilai->user_id = $user->id;
            $penilai->kategori_id = $kategori->id;
            $penilai->nama_penilai = $request->nama_penilai;
            $penilai->alamat_penilai = $request->alamat_penilai;
            $penilai->noTelp_penilai = $request->noTelp_penilai;
            $penilai->bidang_ahli = $request->nama_seni;
            $penilai->lembaga = $request->lembaga;
            $penilai->tgl_lahir = $tgl_lahir;
            $penilai->status_penilai = $request->status_penilai;
            $penilai->kuota = $request->kuota;

            $penilai->save();

            Log::info('Data Penilai Berhasil Diupdate');
            return response()->json([
                'data' => $penilai,
                'status' => 'success',
                'message' => 'Data Penilai Berhasil Diupdate',
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
            $penilai = Penilai::whereNull('deleted_at')->find($id);

            if (!$penilai) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilai tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $penilai,
                'status' => 'success',
                'message' => 'Data Penilai Berhasil Ditampilkan',
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
            $penilai = Penilai::whereNull('deleted_at')->find($id);

            if (!$penilai) {
                Log::error('Data Penilai Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilai Tidak Ditemukan',
                ], 404);
            }

            if ($penilai->delete()) {
                Log::info('Data Penilai Berhasil Dihapus');
                return response()->json([
                    'data' => $penilai,
                    'status' => 'success',
                    'message' => 'Data Penilai Berhasil Dihapus',
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

    public function showLaporan()
    {
        try {
            $penilai = Penilai::whereNull('deleted_at')->get();

            if (count($penilai) > 0) {
                Log::info('Data Penilai Berhasil Ditampilkan');
                return response()->json([
                    'data' => $penilai,
                    'status' => 'success',
                    'message' => 'Data Penilai Berhasil Ditampilkan',
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

    public function downloadPenilaiLaporan()
    {
        try {
            $penilais = Penilai::whereNull('deleted_at')
                ->withCount([
                    'rubrik as jumlah_rubrik',
                    'penilaian_karya as jumlah_penilaian_karya'
                ])
                ->get();

            $data = [
                'penilais' => $penilais
            ];

            $pdf = PDF::loadView('pdf.penilai_laporan', $data);

            return $pdf->download('penilai_laporan.pdf');
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getTotalPenilai()
    {
        $totalPenilai = Penilai::whereNull('deleted_at')->count();

        return response()->json(['total_penilais' => $totalPenilai]);
    }



}
