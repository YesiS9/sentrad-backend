<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PenilaianKarya;
use App\Models\Penilai;
use App\Models\Rubrik;
use App\Models\RubrikPenilaian;
use App\Models\KuotaPenilai;
use App\Models\Tingkatan;
use App\Models\RegistrasiIndividu;
use App\Models\RegistrasiKelompok;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PenilaianKaryaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $penilaiId = $request->input('penilai_id');

            if (!$penilaiId) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Penilai ID tidak ditemukan dalam permintaan',
                ], 400);
            }

            $kuota = DB::table('kuota_penilais')
                ->select('id')
                ->where('penilai_id', $penilaiId)
                ->first();

            if (!$kuota) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Kuota tidak ditemukan untuk Penilai ID ini',
                ], 404);
            }

            $kuotaId = $kuota->id;

            $perPage = $request->input('per_page', 10);

            $penilaianKarya = PenilaianKarya::where('kuota_id', $kuotaId)
                ->with(['registrasiIndividu', 'registrasiKelompok'])
                ->select('id','kuota_id', 'regisIndividu_id', 'regisKelompok_id', 'tingkatan_id', 'tgl_penilaian', 'total_nilai', 'komentar')
                ->paginate($perPage);

            if ($penilaianKarya->count() > 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => $penilaianKarya->items(),
                    'current_page' => $penilaianKarya->currentPage(),
                    'last_page' => $penilaianKarya->lastPage(),
                    'per_page' => $penilaianKarya->perPage(),
                    'total' => $penilaianKarya->total(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Data Penilaian Karya tidak ditemukan',
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




    public function getKuotaId(Request $request)
    {
        $request->validate([
            'penilai_id' => 'required|exists:penilais,id',
        ]);

        $kuotaPenilai = DB::table('kuota_penilais')
            ->where('penilai_id', $request->penilai_id)
            ->first();

        if (!$kuotaPenilai) {
            return response()->json(['error' => 'Kuota tidak ditemukan untuk penilai ini.'], 404);
        }

        return response()->json(['kuota_id' => $kuotaPenilai->id, 'kuota_terpakai' => $kuotaPenilai->kuota_terpakai], 200);
    }

    public function store(Request $request)
    {
        try {
            $messages = [
                'kuota_id.required' => 'Kuota wajib diisi.',
                'rubrik_penilaians.required' => 'Rubrik penilaian wajib diisi.',
                'rubrik_penilaians.min' => 'Rubrik penilaian harus memiliki minimal 5 item.',
                'rubrik_penilaians.*.nama_rubrik.required' => 'Nama rubrik pada setiap item wajib diisi.',
                'rubrik_penilaians.*.skor.required' => 'Skor pada setiap rubrik wajib diisi.',
                'rubrik_penilaians.*.skor.numeric' => 'Skor pada setiap rubrik harus berupa angka.',
                'rubrik_penilaians.*.skor.min' => 'Skor pada setiap rubrik harus minimal 0.',
                'rubrik_penilaians.*.skor.max' => 'Skor pada setiap rubrik tidak boleh lebih dari 100.',
                'komentar.string' => 'Komentar harus berupa teks.',
                'komentar.max' => 'Komentar tidak boleh lebih dari 500 karakter.',
            ];
            $validated = $request->validate([
                'kuota_id' => 'required|exists:kuota_penilais,id',
                'regisIndividu_id' => 'nullable|exists:registrasi_individus,id',
                'regisKelompok_id' => 'nullable|exists:registrasi_kelompoks,id',
                'rubrik_penilaians' => 'required|array|min:5',
                'rubrik_penilaians.*.nama_rubrik' => 'required|exists:rubriks,nama_rubrik',
                'rubrik_penilaians.*.skor' => 'required|numeric|min:0|max:100',
                'komentar' => 'nullable|string|max:500',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $total_nilai = array_sum(array_column($validated['rubrik_penilaians'], 'skor'));

            $penilai = Penilai::find($validated['kuota_id']);
            $kuotaPenilai = KuotaPenilai::find($validated['kuota_id']);

            if ($kuotaPenilai && $penilai && $kuotaPenilai->kuota_terpakai >= $penilai->kuota) {
                return response()->json(['error' => 'Kuota penilai sudah habis.'], 400);
            }

            $tingkatan = Tingkatan::where('nilai_min', '<=', $total_nilai)
                ->where('nilai_max', '>=', $total_nilai)
                ->first();

            if (!$tingkatan) {
                return response()->json(['error' => 'Total nilai tidak sesuai dengan tingkatan yang tersedia.'], 400);
            }

            $penilaianKarya = PenilaianKarya::create([
                'kuota_id' => $validated['kuota_id'],
                'regisIndividu_id' => $validated['regisIndividu_id'],
                'regisKelompok_id' => $validated['regisKelompok_id'],
                'tgl_penilaian' => now(),
                'total_nilai' => $total_nilai,
                'komentar' => $validated['komentar'],
                'tingkatan_id' => $tingkatan->id,
            ]);

            foreach ($validated['rubrik_penilaians'] as $rubrik) {
                $rubrikId = Rubrik::where('nama_rubrik', $rubrik['nama_rubrik'])->value('id');
                if ($rubrikId) {
                    RubrikPenilaian::create([
                        'rubrik_id' => $rubrikId,
                        'penilaian_karya_id' => $penilaianKarya->id,
                        'skor' => $rubrik['skor'],
                    ]);
                }
            }

            $kuotaPenilai->increment('kuota_terpakai');
            if ($validated['regisIndividu_id']) {
                $registrasiIndividu = RegistrasiIndividu::find($validated['regisIndividu_id']);
                $registrasiIndividu->update(['status_individu' => 'Penilaian Selesai']);

                $seniman = $registrasiIndividu->seniman;
                if ($seniman) {
                    $seniman->update(['tingkatan_id' => $tingkatan->id]);
                }
            }

            if ($validated['regisKelompok_id']) {
                $registrasiKelompok = RegistrasiKelompok::find($validated['regisKelompok_id']);
                $registrasiKelompok->update(['status_kelompok' => 'Penilaian Selesai']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data has been saved successfully.',
                'data' => $penilaianKarya,
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $messages = [
                'rubrik_penilaians.required' => 'Rubrik penilaian wajib diisi.',
                'rubrik_penilaians.min' => 'Rubrik penilaian harus memiliki minimal 5 item.',
                'rubrik_penilaians.*.nama_rubrik.required' => 'Nama rubrik pada setiap item wajib diisi.',
                'rubrik_penilaians.*.skor.required' => 'Skor pada setiap rubrik wajib diisi.',
                'rubrik_penilaians.*.skor.numeric' => 'Skor pada setiap rubrik harus berupa angka.',
                'rubrik_penilaians.*.skor.min' => 'Skor pada setiap rubrik harus minimal 0.',
                'rubrik_penilaians.*.skor.max' => 'Skor pada setiap rubrik tidak boleh lebih dari 100.',
                'komentar.string' => 'Komentar harus berupa teks.',
                'komentar.max' => 'Komentar tidak boleh lebih dari 500 karakter.',
            ];
            $validated = $request->validate([
                'rubrik_penilaians' => 'required|array|min:5',
                'rubrik_penilaians.*.nama_rubrik' => 'required|exists:rubriks,nama_rubrik',
                'rubrik_penilaians.*.skor' => 'required|numeric|min:0|max:100',
                'komentar' => 'nullable|string|max:500',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $total_nilai = array_sum(array_column($validated['rubrik_penilaians'], 'skor'));

            $penilaianKarya = PenilaianKarya::findOrFail($id);

            $penilaianKarya->update([
                'total_nilai' => $total_nilai,
                'komentar' => $validated['komentar'],
                'tgl_penilaian' => now(),
            ]);

            foreach ($validated['rubrik_penilaians'] as $rubrikData) {
                $rubrikId = DB::table('rubriks')
                    ->where('nama_rubrik', $rubrikData['nama_rubrik'])
                    ->value('id');

                $rubrikPenilaian = RubrikPenilaian::where('penilaian_karya_id', $id)
                    ->where('rubrik_id', $rubrikId)
                    ->first();

                if ($rubrikPenilaian) {
                    $rubrikPenilaian->update(['skor' => $rubrikData['skor']]);
                }
            }

            $tingkatan = DB::table('tingkatans')
                ->where('nilai_min', '<=', $total_nilai)
                ->where('nilai_max', '>=', $total_nilai)
                ->first();

            if (!$tingkatan) {
                return response()->json(['error' => 'Total nilai tidak sesuai dengan tingkatan yang tersedia.'], 400);
            }

            $penilaianKarya->update(['tingkatan_id' => $tingkatan->id]);

            if ($penilaianKarya->regisIndividu_id) {
                $senimanId = DB::table('registrasi_individus')
                    ->where('id', $penilaianKarya->regisIndividu_id)
                    ->value('seniman_id');

                if ($senimanId) {
                    DB::table('seniman')
                        ->where('id', $senimanId)
                        ->update(['tingkatan_id' => $tingkatan->id]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data has been updated successfully.',
                'data' => $penilaianKarya,
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $penilaianKarya = PenilaianKarya::whereNull('deleted_at')->find($id);

            if (!$penilaianKarya) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilaian Karya tidak ditemukan',
                ], 404);
            }

            $rubrikPenilaians = RubrikPenilaian::where('penilaian_karya_id', $penilaianKarya->id)
                ->join('rubriks', 'rubrik_penilaians.rubrik_id', '=', 'rubriks.id')
                ->select('rubriks.nama_rubrik', 'rubrik_penilaians.skor')
                ->get();

            return response()->json([
                'data' => [
                    'penilaianKarya' => $penilaianKarya,
                    'rubrik_penilaians' => $rubrikPenilaians
                ],
                'status' => 'success',
                'message' => 'Data Penilaian Karya Berhasil Ditampilkan',
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
            $penilaianKarya = PenilaianKarya::whereNull('deleted_at')->find($id);

            if (!$penilaianKarya) {
                Log::error('Data Penilaian Karya Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilaian Karya Tidak Ditemukan',
                ], 404);
            }

            if ($penilaianKarya->delete()) {
                Log::info('Data Penilaian Karya Berhasil Dihapus');
                return response()->json([
                    'data' => $penilaianKarya,
                    'status' => 'success',
                    'message' => 'Data Penilaian Karya Berhasil Dihapus',
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

    public function updateSenimanTingkatan($penilaianKaryaId)
    {
        $penilaian = PenilaianKarya::find($penilaianKaryaId);

        if ($penilaian) {
            $skor = $penilaian->skor;
            $tingkatan = Tingkatan::where('nilai_min', '<=', $skor)
                                ->where('nilai_max', '>=', $skor)
                                ->first();

            if ($tingkatan) {
                $seniman = Seniman::find($penilaian->seniman_id);
                $seniman->tingkatan_id = $tingkatan->id;
                $seniman->save();
            }
        }
    }

    public function showByRegistrationId($id)
    {
        try {
            $penilaianKarya = PenilaianKarya::whereNull('deleted_at')
                ->where(function ($query) use ($id) {
                    $query->where('regisIndividu_id', $id)
                          ->orWhere('regisKelompok_id', $id);
                })
                ->first();

            if (!$penilaianKarya) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Penilaian Karya tidak ditemukan',
                ], 404);
            }

            $rubrikPenilaians = RubrikPenilaian::where('penilaian_karya_id', $penilaianKarya->id)
                ->join('rubriks', 'rubrik_penilaians.rubrik_id', '=', 'rubriks.id')
                ->select('rubriks.nama_rubrik', 'rubrik_penilaians.skor')
                ->get();

            return response()->json([
                'data' => [
                    'penilaianKarya' => $penilaianKarya,
                    'rubrik_penilaians' => $rubrikPenilaians,
                ],
                'status' => 'success',
                'message' => 'Data Penilaian Karya Berhasil Ditampilkan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}
