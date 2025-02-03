<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrasiKelompok;
use App\Models\Seniman;
use App\Models\KategoriSeni;
use App\Models\Penilai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterKelompokController extends Controller
{
    public function index(Request $request){
        try {
            $perPage = $request->input('per_page', 10);
            $register = RegistrasiKelompok::whereNull('deleted_at')->paginate($perPage);

            if (count($register) > 0) {
                Log::info('Data Registrasi Kelompok Berhasil Ditampilkan');
                return response()->json([
                    'data' => $register->items(),
                    'current_page' => $register->currentPage(),
                    'per_page' => $register->perPage(),
                    'total' => $register->total(),
                    'last_page' => $register->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Registrasi Kelompok Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Penilai Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Kosong',
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

    public function indexForPenilai(Request $request, $penilai_id)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $penilai = Penilai::find($penilai_id);
            if (!$penilai) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Penilai tidak ditemukan',
                ], 404);
            }

            $kategoriIdPenilai = $penilai->kategori_id;
            if (!$kategoriIdPenilai) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Penilai tidak memiliki kategori ID',
                ], 404);
            }

            $register = RegistrasiKelompok::where('status_kelompok', 'Dalam proses')
                ->whereNull('deleted_at')
                ->whereHas('seniman', function ($query) use ($kategoriIdPenilai) {
                    $query->where('kategori_id', $kategoriIdPenilai);
                })
                ->with('seniman:id,nama_seniman')
                ->paginate($perPage);

            if ($register->count() > 0) {
                Log::info('Data Registrasi Kelompok Berhasil Ditampilkan');

                return response()->json([
                    'data' => $register->items(),
                    'current_page' => $register->currentPage(),
                    'per_page' => $register->perPage(),
                    'total' => $register->total(),
                    'last_page' => $register->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Registrasi Kelompok Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Registrasi Kelompok Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Kosong',
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

    public function getRegistrasiKelompok()
    {
        try {
            $user = Auth::user();
            $id= Auth::id();
            if (!$user->seniman) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Seniman not found for the user',
                ], 404);
            }

            $senimanId = $user->seniman->id;

            $kelompok = RegistrasiKelompok::where('seniman_id', $senimanId)
                ->select('id','nama_kelompok', 'created_at', 'status_kelompok')
                ->paginate(10);

            if ($kelompok->count() > 0) {
                return response()->json([
                    'status' => 'success',
                     'data' => $kelompok->items(),
                    'id' => $id,
                    'current_page' => $kelompok->currentPage(),
                    'last_page' => $kelompok->lastPage(),
                    'per_page' => $kelompok->perPage(),
                    'total' => $kelompok->total(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Data Registrasi Kelompok tidak ditemukan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $storeData = $request->all();

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.exists' => 'Nama kategori yang dipilih tidak valid.',
                'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
                'nama_kelompok.unique' => 'Nama kelompok sudah digunakan, harap pilih nama yang lain.',
                'tgl_terbentuk.required' => 'Tanggal terbentuk wajib diisi.',
                'tgl_terbentuk.date_format' => 'Format tanggal terbentuk harus sesuai dengan format dd/mm/yyyy.',
                'alamat_kelompok.required' => 'Alamat kelompok wajib diisi.',
                'deskripsi_kelompok.required' => 'Deskripsi kelompok wajib diisi.',
                'noTelp_kelompok.required' => 'Nomor telepon kelompok wajib diisi.',
                'noTelp_kelompok.regex' => 'Nomor telepon kelompok harus dimulai dengan 08 dan terdiri dari 9 hingga 13 digit.',
                'email_kelompok.required' => 'Email kelompok wajib diisi.',
                'email_kelompok.email' => 'Format email kelompok tidak valid.',
                'jumlah_anggota.required' => 'Jumlah anggota wajib diisi.',
                'jumlah_anggota.numeric' => 'Jumlah anggota harus berupa angka.',
                'status_kelompok.required' => 'Status kelompok wajib diisi.',
            ];

            $validate = Validator::make($storeData, [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama_kelompok' => 'required|unique:registrasi_kelompoks,nama_kelompok',
                'tgl_terbentuk' => 'required|date_format:d/m/Y',
                'alamat_kelompok' => 'required',
                'deskripsi_kelompok' => 'required',
                'noTelp_kelompok' => 'required|regex:/^08\d{8,12}$/',
                'email_kelompok' => 'required|email',
                'jumlah_anggota' => 'required|numeric',
                'status_kelompok' => 'required',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $seniman = $user->seniman;

            if (!$seniman) {
                Log::error('Seniman not logged in');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Seniman not logged in',
                ], 401);
            }

            $kategori = KategoriSeni::where('nama_kategori', $storeData['nama_kategori'])->first();
            if (!$kategori) {
                Log::error('Kategori tidak ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori tidak ditemukan',
                ], 400);
            }

            $storeData['kategori_id'] = $kategori->id;
            $storeData['tgl_terbentuk'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_terbentuk'])->format('Y-m-d');
            $storeData['seniman_id'] = $seniman->id;

            $register = RegistrasiKelompok::create($storeData);

            Log::info('Data Registrasi Kelompok Berhasil Ditambahkan');
            return response()->json([
                'data' => $register,
                'kelompok_id' => $register->id,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Ditambahkan',
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




    public function storebyAdmin(Request $request)
    {
        try {
            $storeData = $request->all();

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.exists' => 'Nama kategori yang dipilih tidak valid.',
                'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
                'nama_kelompok.unique' => 'Nama kelompok sudah digunakan, harap pilih nama yang lain.',
                'tgl_terbentuk.required' => 'Tanggal terbentuk wajib diisi.',
                'tgl_terbentuk.date_format' => 'Format tanggal terbentuk harus sesuai dengan format dd/mm/yyyy.',
                'alamat_kelompok.required' => 'Alamat kelompok wajib diisi.',
                'deskripsi_kelompok.required' => 'Deskripsi kelompok wajib diisi.',
                'noTelp_kelompok.required' => 'Nomor telepon kelompok wajib diisi.',
                'noTelp_kelompok.regex' => 'Nomor telepon kelompok harus dimulai dengan 08 dan terdiri dari 9 hingga 13 digit.',
                'email_kelompok.required' => 'Email kelompok wajib diisi.',
                'email_kelompok.email' => 'Format email kelompok tidak valid.',
                'jumlah_anggota.required' => 'Jumlah anggota wajib diisi.',
                'jumlah_anggota.numeric' => 'Jumlah anggota harus berupa angka.',
                'status_kelompok.required' => 'Status kelompok wajib diisi.',
            ];

            $validate = Validator::make($storeData, [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama_seniman'=>'required|exists:seniman,nama_seniman',
                'nama_kelompok' => 'required|unique:registrasi_kelompoks,nama_kelompok',
                'tgl_terbentuk' => 'required|date_format:d/m/Y',
                'alamat_kelompok' => 'required',
                'deskripsi_kelompok' => 'required',
                'noTelp_kelompok' => 'required|regex:/^08\d{8,12}$/',
                'email_kelompok' => 'required|email',
                'jumlah_anggota' => 'required|numeric',
                'status_kelompok' => 'required',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $seniman = Seniman::where('nama_seniman', $request->nama_seniman)->first();

            if (!$seniman) {
                Log::error('Seniman not found with nama_seniman: ' . $request->nama_seniman);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Seniman not found',
                ], 404);
            }

            $kategori = KategoriSeni::where('nama_kategori', $storeData['nama_kategori'])->first();
            if (!$kategori) {
                Log::error('Kategori Seni tidak ditemukan dengan nama_kategori: ' . $request->nama_kategori);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori Seni tidak ditemukan',
                ], 404);
            }

            $storeData['tgl_terbentuk'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_terbentuk'])->format('Y-m-d');
            $storeData['seniman_id'] = $seniman->id;
            $storeData['kategori_id'] = $kategori->id;
            unset($storeData['nama_seniman']);
            unset($storeData['nama_kategori']);

            $register = RegistrasiKelompok::create($storeData);

            Log::info('Data Registrasi Kelompok Berhasil Ditambahakan');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Ditambahakan',
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
            $register = RegistrasiKelompok::with('kategoriSeni')->where('id', $id)->whereNull('deleted_at')->find($id);
            if (!$register) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Kelompok tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Ditampilkan',
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

    public function showByAdmin($id)
    {
        try {
            $register = RegistrasiKelompok::with('seniman')->find($id);

            if (!$register) {
                Log::error('RegistrasiKelompok not found with ID: ' . $id);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'RegistrasiKelompok not found',
                ], 404);
            }

            $data = [
                'id' => $register->id,
                'nama_seniman' => $register->seniman->nama_seniman,
                'nama_kelompok' => $register->nama_kelompok,
                'tgl_terbentuk' => $register->tgl_terbentuk,
                'alamat_kelompok' => $register->alamat_kelompok,
                'deskripsi_kelompok' => $register->deskripsi_kelompok,
                'noTelp_kelompok' => $register->noTelp_kelompok,
                'email_kelompok' => $register->email_kelompok,
                'jumlah_anggota' => $register->jumlah_anggota,
                'status_kelompok' => $register->status_kelompok,
                'created_at' => $register->created_at,
                'updated_at' => $register->updated_at,
            ];

            Log::info('Data Registrasi Kelompok Berhasil Ditampilkan');
            return response()->json([
                'data' => $data,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Ditampilkan',
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
            $register = RegistrasiKelompok::whereNull('deleted_at')->find($id);

            if (!$register) {
                Log::error('Data Registrasi Kelompok Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Kelompok Tidak Ditemukan',
                ], 404);
            }

            if ($register->status_kelompok !== 'dalam proses') {
                Log::error('Status kelompok harus bernilai "dalam proses" untuk melakukan pembaruan.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Penilaian registrasi telah selesai sehingga pembaruan data tidak dapat dilakukan.',
                ], 400);
            }

            if (Carbon::now()->diffInHours($register->created_at) > 24) {
                Log::error('Waktu pembuatan lebih dari 24 jam. Pembaruan tidak diperbolehkan.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Pembaruan hanya dapat dilakukan jika waktu pendaftaran kurang dari 24 jam.',
                ], 400);
            }

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.exists' => 'Nama kategori yang dipilih tidak valid.',
                'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
                'nama_kelompok.unique' => 'Nama kelompok sudah digunakan, harap pilih nama yang lain.',
                'tgl_terbentuk.required' => 'Tanggal terbentuk wajib diisi.',
                'tgl_terbentuk.date_format' => 'Format tanggal terbentuk harus sesuai dengan format dd/mm/yyyy.',
                'alamat_kelompok.required' => 'Alamat kelompok wajib diisi.',
                'deskripsi_kelompok.required' => 'Deskripsi kelompok wajib diisi.',
                'noTelp_kelompok.required' => 'Nomor telepon kelompok wajib diisi.',
                'noTelp_kelompok.regex' => 'Nomor telepon kelompok harus dimulai dengan 08 dan terdiri dari 9 hingga 13 digit.',
                'email_kelompok.required' => 'Email kelompok wajib diisi.',
                'email_kelompok.email' => 'Format email kelompok tidak valid.',
                'jumlah_anggota.required' => 'Jumlah anggota wajib diisi.',
                'jumlah_anggota.numeric' => 'Jumlah anggota harus berupa angka.',
                'status_kelompok.required' => 'Status kelompok wajib diisi.',
            ];

            $validate = Validator::make($request->all(), [
                'seniman_id' => 'required|exists:seniman,id',
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama_kelompok' => 'required',
                'tgl_terbentuk' => 'required|date_format:d/m/Y',
                'alamat_kelompok' => 'required',
                'deskripsi_kelompok' => 'required',
                'noTelp_kelompok' => 'required|regex:/^08\d{8,12}$/',
                'email_kelompok' => 'required|email',
                'jumlah_anggota' => 'required|numeric',
                'status_kelompok' => 'required',
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
                Log::error('Kategori not found with nama_kategori: ' . $request->nama_kategori);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori Seni tidak ditemukan',
                ], 404);
            }

            $tgl_terbentuk = Carbon::createFromFormat('d/m/Y', $request->tgl_terbentuk)->format('Y-m-d');

            $register->seniman_id = $request->seniman_id;
            $register->nama_kelompok = $request->nama_kelompok;
            $register->tgl_terbentuk = $tgl_terbentuk;
            $register->alamat_kelompok = $request->alamat_kelompok;
            $register->deskripsi_kelompok = $request->deskripsi_kelompok;
            $register->noTelp_kelompok = $request->noTelp_kelompok;
            $register->email_kelompok = $request->email_kelompok;
            $register->jumlah_anggota = $request->jumlah_anggota;
            $register->status_kelompok = $request->status_kelompok;
            $register->kategori_id = $kategori->id;

            $register->save();

            Log::info('Data Registrasi Kelompok Berhasil Diupdate');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok Berhasil Diupdate',
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


    public function updateByAdmin(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama_seniman' => 'required|exists:seniman,nama_seniman',
                'nama_kelompok' => 'required|unique:registrasi_kelompoks,nama_kelompok,' . $id,
                'tgl_terbentuk' => 'required|date_format:d/m/Y',
                'alamat_kelompok' => 'required',
                'deskripsi_kelompok' => 'required',
                'noTelp_kelompok' => 'required|regex:/^08\d{8,12}$/',
                'email_kelompok' => 'required|email',
                'jumlah_anggota' => 'required|numeric',
                'status_kelompok' => 'required',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $register = RegistrasiKelompok::find($id);
            if (!$register) {
                Log::error('Data Registrasi Kelompok tidak ditemukan dengan ID: ' . $id);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Kelompok tidak ditemukan',
                ], 404);
            }


            $seniman = Seniman::where('nama_seniman', $request->nama_seniman)->first();
            if (!$seniman) {
                Log::error('Seniman tidak ditemukan dengan nama_seniman: ' . $request->nama_seniman);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Seniman tidak ditemukan',
                ], 404);
            }

            $kategori = KategoriSeni::where('nama_kategori', $request->nama_kategori)->first();
            if (!$kategori) {
                Log::error('Kategori Seni tidak ditemukan dengan nama_kategori: ' . $request->nama_kategori);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori Seni tidak ditemukan',
                ], 404);
            }


            $tgl_terbentuk = Carbon::createFromFormat('d/m/Y', $request->tgl_terbentuk)->format('Y-m-d');

            $register->update([
                'kategori_id' => $kategori->id,
                'seniman_id' => $seniman->id,
                'nama_kelompok' => $request->nama_kelompok,
                'tgl_terbentuk' => $tgl_terbentuk,
                'alamat_kelompok' => $request->alamat_kelompok,
                'deskripsi_kelompok' => $request->deskripsi_kelompok,
                'noTelp_kelompok' => $request->noTelp_kelompok,
                'email_kelompok' => $request->email_kelompok,
                'jumlah_anggota' => $request->jumlah_anggota,
                'status_kelompok' => $request->status_kelompok,
            ]);

            Log::info('Data Registrasi Kelompok berhasil diupdate dengan ID: ' . $id);
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Kelompok berhasil diupdate',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }





    public function destroy($id){
        try {
            $register = RegistrasiKelompok::whereNull('deleted_at')->find($id);

            if (!$register) {
                Log::error('Data Registrasi Kelompok Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Kelompok Tidak Ditemukan',
                ], 404);
            }

            if ($register->delete()) {
                Log::info('Data Registrasi Kelompok Berhasil Dihapus');
                return response()->json([
                    'data' => $register,
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

    public function getTotalKelompok()
    {
        $totalKelompok = RegistrasiKelompok::whereNull('deleted_at')->count();

        return response()->json(['total_kelompoks' => $totalKelompok]);
    }



}
