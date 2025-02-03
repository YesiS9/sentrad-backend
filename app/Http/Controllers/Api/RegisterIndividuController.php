<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrasiIndividu;
use App\Models\Seniman;
use App\Models\Penilai;
use App\Models\KategoriSeni;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RegisterIndividuController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $register = RegistrasiIndividu::whereNull('deleted_at')->paginate($perPage);

            if ($register->count() > 0) {
                Log::info('Data Registrasi Individu Berhasil Ditampilkan');
                return response()->json([
                    'data' => $register->items(),
                    'current_page' => $register->currentPage(),
                    'per_page' => $register->perPage(),
                    'total' => $register->total(),
                    'last_page' => $register->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Registrasi Individu Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Registrasi Individu Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Kosong',
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

    public function getRegistrasiIndividu()
    {
        try {
            $user = Auth::user();
            $id = Auth::id();
            if (!$user->seniman) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Seniman not found for the user',
                ], 404);
            }

            $senimanId = $user->seniman->id;

            $individu = RegistrasiIndividu::where('seniman_id', $senimanId)
                ->select('id', 'nama', 'created_at', 'status_individu')
                ->paginate(10);

            if ($individu->count() > 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => $individu->items(),
                    'id' => $id,
                    'current_page' => $individu->currentPage(),
                    'last_page' => $individu->lastPage(),
                    'per_page' => $individu->perPage(),
                    'total' => $individu->total(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Data Registrasi Individu tidak ditemukan',
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

    public function indexForPorto($individu_id)
    {

        $registrasiIndividu = RegistrasiIndividu::where('individu_id', $individu_id)->first();

        if (!$registrasiIndividu) {
            return response()->json(['message' => 'Data Registrasi Individu tidak ditemukan'], 404);
        }

        $seniman = $registrasiIndividu->seniman;

        if (!$seniman) {
            return response()->json(['message' => 'Seniman tidak ditemukan'], 404);
        }

        $portofolios = $seniman->portofolios;

        return response()->json([
            'registrasi' => $registrasiIndividu,
            'portofolios' => $portofolios
        ]);
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

            $register = RegistrasiIndividu::where('status_individu', 'Dalam proses')
                ->whereNull('deleted_at')
                ->whereHas('seniman', function ($query) use ($kategoriIdPenilai) {
                    $query->where('kategori_id', $kategoriIdPenilai);
                })
                ->with('seniman:id,nama_seniman')
                ->paginate($perPage);

            if ($register->count() > 0) {
                Log::info('Data Registrasi Individu Berhasil Ditampilkan');

                return response()->json([
                    'data' => $register->items(),
                    'current_page' => $register->currentPage(),
                    'per_page' => $register->perPage(),
                    'total' => $register->total(),
                    'last_page' => $register->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Registrasi Individu Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Registrasi Individu Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Kosong',
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
            $user = Auth::user();
            $storeData = $request->all();

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.exists' => 'Nama kategori yang dipilih tidak valid.',
                'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
                'tgl_lahir.date_format' => 'Format tanggal lahir harus sesuai dengan format dd/mm/yyyy.',
                'tgl_mulai.required' => 'Tanggal mulai berkarya wajib diisi.',
                'tgl_mulai.date_format' => 'Format tanggal mulai harus sesuai dengan format dd/mm/yyyy.',
                'alamat.required' => 'Alamat wajib diisi.',
                'noTelp.required' => 'Nomor telepon wajib diisi.',
                'noTelp.regex' => 'Nomor telepon harus dimulai dengan 08 dan terdiri dari 9 hingga 13 digit.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'status_individu.required' => 'Status individu wajib diisi.',
            ];

            $validate = Validator::make($storeData, [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'tgl_mulai' => 'required|date_format:d/m/Y',
                'alamat' => 'required',
                'noTelp' => 'required|regex:/^08\d{8,12}$/',
                'email' => 'required|email',
                'status_individu' => 'required',
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


            $existingRegistration = RegistrasiIndividu::where('seniman_id', $seniman->id)->first();
            if ($existingRegistration) {
                Log::error('User has already registered an individual: ' . $existingRegistration->id);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'You can only register once for an individual.',
                ], 400);
            }

            $kategori = KategoriSeni::where('nama_kategori', $storeData['nama_kategori'])->first();
            if (!$kategori) {
                Log::error('Kategori Seni not found with nama_kategori: ' . $storeData['nama_kategori']);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori Seni not found',
                ], 404);
            }

            $storeData['nama'] = $seniman->nama_seniman;
            $storeData['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_lahir'])->format('Y-m-d');
            $storeData['tgl_mulai'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_mulai'])->format('Y-m-d');
            $storeData['seniman_id'] = $seniman->id;
            $storeData['kategori_id'] = $kategori->id;
            unset($storeData['nama_kategori']);

            $register = RegistrasiIndividu::create($storeData);

            Log::info('Data Registrasi Individu Berhasil Ditambahkan');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Berhasil Ditambahkan',
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
                'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
                'tgl_lahir.date_format' => 'Format tanggal lahir harus sesuai dengan format dd/mm/yyyy.',
                'tgl_mulai.required' => 'Tanggal mulai berkarya wajib diisi.',
                'tgl_mulai.date_format' => 'Format tanggal mulai harus sesuai dengan format dd/mm/yyyy.',
                'alamat.required' => 'Alamat wajib diisi.',
                'noTelp.required' => 'Nomor telepon wajib diisi.',
                'noTelp.regex' => 'Nomor telepon harus dimulai dengan 08 dan terdiri dari 9 hingga 13 digit.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'status_individu.required' => 'Status individu wajib diisi.',
            ];

            $validate = Validator::make($storeData, [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama_seniman' => 'required|exists:seniman,nama_seniman',
                'nama' => 'required|unique:registrasi_individus,nama',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'tgl_mulai' => 'required|date_format:d/m/Y',
                'alamat' => 'required',
                'noTelp' => 'required|regex:/^08\d{8,12}$/',
                'email' => 'required|email',
                'status_individu' => 'required',
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

            if ($storeData['nama'] !== $seniman->nama_seniman) {
                Log::error('Nama tidak sesuai dengan nama_seniman: ' . $storeData['nama']);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Nama harus sama dengan nama seniman',
                ], 400);
            }

            $storeData['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_lahir'])->format('Y-m-d');
            $storeData['tgl_mulai'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_mulai'])->format('Y-m-d');
            $storeData['seniman_id'] = $seniman->id;
            $storeData['kategori_id'] = $kategori->id;
            unset($storeData['nama_seniman']);
            unset($storeData['nama_kategori']);

            $register = RegistrasiIndividu::create($storeData);

            Log::info('Data Registrasi Individu Berhasil Ditambahkan');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Berhasil Ditambahkan',
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
        $registrasiIndividu = RegistrasiIndividu::with('kategoriSeni')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->find($id);

        if ($registrasiIndividu) {
            return response()->json([
                'status' => 'success',
                'data' => $registrasiIndividu
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data not found'
        ], 404);
    }


    public function showByAdmin($id)
    {
        try {
            $register = RegistrasiIndividu::with(['seniman:id,nama_seniman'])->find($id);
            if ($register) {
                return response()->json([
                    'data' => $register,
                    'status' => 'success',
                    'message' => 'Data Registrasi Individu Berhasil Ditampilkan',
                ], 200);
            }

            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Registrasi Individu tidak ditemukan',
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
            $register = RegistrasiIndividu::whereNull('deleted_at')->find($id);

            if (!$register) {
                Log::error('Data Registrasi Individu Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Individu Tidak Ditemukan',
                ], 404);
            }

            if ($register->status_individu !== 'dalam proses') {
                Log::error('Status individu bukan "dalam proses".');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Penilaian registrasi telah selesai sehingga pembaruan data tidak dapat dilakukan.',
                ], 400);
            }

            if (Carbon::now()->diffInHours($register->created_at) > 24) {
                Log::error('Waktu pembuatan lebih dari 24 jam.');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Pembaruan hanya dapat dilakukan jika waktu pendaftaran kurang dari 24 jam.',
                ], 400);
            }

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.exists' => 'Nama kategori yang dipilih tidak valid.',
                'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
                'tgl_lahir.date_format' => 'Format tanggal lahir harus sesuai dengan format dd/mm/yyyy.',
                'tgl_mulai.required' => 'Tanggal mulai berkarya wajib diisi.',
                'tgl_mulai.date_format' => 'Format tanggal mulai harus sesuai dengan format dd/mm/yyyy.',
                'alamat.required' => 'Alamat wajib diisi.',
                'noTelp.required' => 'Nomor telepon wajib diisi.',
                'noTelp.regex' => 'Nomor telepon harus dimulai dengan 08 dan terdiri dari 9 hingga 13 digit.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'status_individu.required' => 'Status individu wajib diisi.',
            ];

            $validate = Validator::make($request->all(), [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama' => 'required',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'tgl_mulai' => 'required|date_format:d/m/Y',
                'alamat' => 'required',
                'noTelp' => 'required|regex:/^08\d{8,12}$/',
                'email' => 'required|email',
                'status_individu' => 'required',
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
                Log::error('Kategori Seni tidak ditemukan dengan nama_kategori: ' . $request->nama_kategori);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori Seni tidak ditemukan',
                ], 404);
            }

            $register->kategori_id = $kategori->id;
            $register->tgl_lahir = Carbon::createFromFormat('d/m/Y', $request->tgl_lahir)->format('Y-m-d');
            $register->tgl_mulai = Carbon::createFromFormat('d/m/Y', $request->tgl_mulai)->format('Y-m-d');


            $register->nama = $request->input('nama', $register->nama);
            $register->alamat = $request->input('alamat', $register->alamat);
            $register->noTelp = $request->input('noTelp', $register->noTelp);
            $register->email = $request->input('email', $register->email);
            $register->status_individu = $request->input('status_individu', $register->status_individu);

            $register->save();

            Log::info('Data Registrasi Individu Berhasil Diperbarui');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Berhasil Diperbarui',
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
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'nama_kategori' => 'required|exists:kategori_senis,nama_kategori',
                'nama_seniman' => 'required|exists:seniman,nama_seniman',
                'nama' => 'required',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'tgl_mulai' => 'required|date_format:d/m/Y',
                'alamat' => 'required',
                'noTelp' => 'required|regex:/^08\d{8,12}$/',
                'email' => 'required|email',
                'status_individu' => 'required',
            ]);

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

            $kategori = KategoriSeni::where('nama_kategori', $request->nama_kategori)->first();
            if (!$kategori) {
                Log::error('Kategori Seni tidak ditemukan dengan nama_kategori: ' . $request->nama_kategori);
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Kategori Seni tidak ditemukan',
                ], 404);
            }

            $register = RegistrasiIndividu::find($id);
            if (!$register) {
                Log::info('Data Registrasi Individu tidak ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Individu tidak ditemukan',
                ], 404);
            }

            $register->update([
                'nama' => $storeData['nama'],
                'tgl_lahir' => Carbon::createFromFormat('d/m/Y', $storeData['tgl_lahir'])->format('Y-m-d'),
                'tgl_mulai' => Carbon::createFromFormat('d/m/Y', $storeData['tgl_mulai'])->format('Y-m-d'),
                'alamat' => $storeData['alamat'],
                'noTelp' => $storeData['noTelp'],
                'email' => $storeData['email'],
                'status_individu' => $storeData['status_individu'],
                'seniman_id' => $seniman->id,
                'kategori_id' => $kategori->id,
            ]);

            Log::info('Data Registrasi Individu Berhasil Diubah');
            return response()->json([
                'data' => $register,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Berhasil Diubah',
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
            $register = RegistrasiIndividu::find($id);
            if (!$register) {
                Log::info('Data Registrasi Individu tidak ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Registrasi Individu tidak ditemukan',
                ], 404);
            }

            $register->delete();

            Log::info('Data Registrasi Individu Berhasil Dihapus');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Registrasi Individu Berhasil Dihapus',
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

    public function getTotalIndividu()
    {
        $totalIndividu = RegistrasiIndividu::whereNull('deleted_at')->count();

        return response()->json(['total_individus' => $totalIndividu]);
    }
}
