<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Seniman;
use App\Models\RegistrasiIndividu;
use App\Models\RegistrasiKelompok;
use App\Models\KategoriSeni;
use App\Models\Penilai;
use App\Models\PenilaianKarya;
use PDF;

class LaporanController extends Controller
{
    public function downloadUserData()
    {
        $users = User::with('roles')->whereNull('deleted_at')->get(['username', 'email', 'created_at']);

        if ($users->isEmpty()) {
            return response()->json(['message' => 'Data User tidak ditemukan'], 404);
        }

        $data = ['users' => $users];
        $pdf = PDF::loadView('pdf.user_data', $data);

        return $pdf->download('data_user.pdf');
    }

    public function previewLaporanData(Request $request)
    {
        $type = $request->query('type');
        if ($type === 'user') {
            $users = User::with('roles')->whereNull('deleted_at')->get();

            $userData = $users->map(function ($user) {
                return [
                    'username' => $user->username,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('nama_role')->join(', ') ?: 'N/A',
                ];
            });

            if ($userData->isEmpty()) {
                return response()->json(['message' => 'Data User tidak ditemukan'], 404);
            }

            $data = ['users' => $userData];
            $pdf = PDF::loadView('pdf.user_data', $data);

            return $pdf->stream('preview_user_data.pdf');
        } else if($type === 'registrasi'){
            $individuData = RegistrasiIndividu::with('kategoriSeni')
            ->whereNull('deleted_at')
            ->get(['nama', 'kategori_id', 'tgl_lahir', 'tgl_mulai', 'alamat', 'noTelp', 'email', 'status_individu', 'created_at']);

            $kelompokData = RegistrasiKelompok::with('kategoriSeni')
                ->whereNull('deleted_at')
                ->get(['nama_kelompok', 'kategori_id', 'tgl_terbentuk', 'alamat_kelompok', 'noTelp_kelompok', 'email_kelompok', 'deskripsi_kelompok', 'jumlah_anggota', 'status_kelompok', 'created_at']);

            $data = [
                'individuData' => $individuData,
                'kelompokData' => $kelompokData,
            ];

            $pdf = PDF::loadView('pdf.registrasi_data', $data)->setPaper('a4', 'landscape');

            return $pdf->stream('preview_registrasi_data.pdf');
        } else if($type === 'kategori'){
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')
            ->get(['nama_kategori', 'deskripsi_kategori', 'created_at']);

            if ($kategoriSeni->isEmpty()) {
                return response()->json(['message' => 'Data Kategori Seni tidak ditemukan'], 404);
            }

            $data = ['kategoriSeni' => $kategoriSeni];
            $pdf = PDF::loadView('pdf.kategori_seni_data', $data);

            return $pdf->stream('preview_kategori_seni.pdf');
        }else if($type === 'seniman'){
            $senimanData = Seniman::with('tingkatan')
            ->whereNull('deleted_at')
            ->get(['nama_seniman', 'tingkatan_id', 'tgl_lahir', 'deskripsi_seniman', 'alamat_seniman', 'noTelp_seniman', 'lama_pengalaman']);

            $data = [
                'senimanData' => $senimanData,
            ];

            $pdf = PDF::loadView('pdf.seniman_data', $data)->setPaper('a4', 'landscape');

            return $pdf->stream('preview_seniman_data.pdf');
        } else if ($type === 'penilaian') {
                $penilaianData = PenilaianKarya::with([
                    'kuota.penilai',
                    'rubrikPenilaians',
                    'registrasiIndividu',
                    'registrasiKelompok',
                    'tingkatan'
                ])
                ->whereNull('deleted_at')
                ->get();

                if ($penilaianData->isEmpty()) {
                    return response()->json(['message' => 'Data Penilaian Karya tidak ditemukan'], 404);
                }

                $data = $penilaianData->map(function ($penilaian) {
                    return [
                        'nama_penilai' => optional($penilaian->kuota->penilai)->nama_penilai ?? 'N/A',
                        'nama' => $penilaian->registrasiIndividu->nama ?? $penilaian->registrasiKelompok->nama_kelompok ?? 'N/A',
                        'tingkatan' => optional($penilaian->tingkatan)->nama_tingkatan ?? 'N/A',
                        'total_nilai' => $penilaian->total_nilai,
                        'komentar' => $penilaian->komentar,
                        'created_at' => $penilaian->created_at->format('Y-m-d'),
                        'rubrik_penilaian' => $penilaian->rubrikPenilaians->map(function ($rubrik) {
                            return [
                                'nama_rubrik' => $rubrik->nama_rubrik,
                                'skor' => $rubrik->pivot->skor,
                            ];
                        }),
                    ];
                });

                $pdf = PDF::loadView('pdf.penilaian_data', ['penilaianData' => $data])
                    ->setPaper('a4', 'portrait');

                return $pdf->stream('preview_penilaian_data.pdf');
            } else if ($type === 'penilai') {
                $penilaiData = Penilai::with('kategoriSeni')
                    ->whereNull('deleted_at')
                    ->get(['nama_penilai', 'kategori_id', 'tgl_lahir', 'alamat_penilai', 'noTelp_penilai', 'bidang_ahli', 'lembaga', 'kuota']);

                $data = $penilaiData->map(function ($penilai) {
                    return [
                        'nama_penilai' => $penilai->nama_penilai,
                        'nama_kategori' => optional($penilai->kategoriSeni)->nama_kategori,
                        'tgl_lahir' => $penilai->tgl_lahir,
                        'alamat_penilai' => $penilai->alamat_penilai,
                        'noTelp_penilai' => $penilai->noTelp_penilai,
                        'bidang_ahli' => $penilai->bidang_ahli,
                        'lembaga' => $penilai->lembaga,
                        'kuota' => $penilai->kuota,
                    ];
                });

                $pdf = PDF::loadView('pdf.penilai_data', ['penilaiData' => $data])
                    ->setPaper('a4', 'landscape');

                return $pdf->stream('preview_penilai_data.pdf');
            } else{
            return response()->json(['message' => 'Parameter type tidak valid'], 400);
        }

    }

    public function downloadLaporanData(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'user'){
            $users = User::with('roles')->whereNull('deleted_at')->get();

            $userData = $users->map(function ($user) {
                return [
                    'username' => $user->username,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('nama_role')->join(', ') ?: 'N/A',
                ];
            });

            if ($userData->isEmpty()) {
                return response()->json(['message' => 'Data User tidak ditemukan'], 404);
            }

            $data = ['users' => $userData];
            $pdf = PDF::loadView('pdf.user_data', $data);

            return $pdf->download('data_user.pdf');
        } else if ($type === 'registrasi'){
            $individuData = RegistrasiIndividu::with('kategoriSeni')
            ->whereNull('deleted_at')
            ->get(['nama', 'kategori_id', 'tgl_lahir', 'tgl_mulai', 'alamat', 'noTelp', 'email', 'status_individu', 'created_at']);

            $kelompokData = RegistrasiKelompok::with('kategoriSeni')
                ->whereNull('deleted_at')
                ->get(['nama_kelompok', 'kategori_id', 'tgl_terbentuk', 'alamat_kelompok', 'noTelp_kelompok', 'email_kelompok', 'deskripsi_kelompok', 'jumlah_anggota', 'status_kelompok', 'created_at']);

            $data = [
                'individuData' => $individuData,
                'kelompokData' => $kelompokData,
            ];

            $pdf = PDF::loadView('pdf.registrasi_data', $data)->setPaper('a4', 'landscape');

            return $pdf->download('data_registrasi.pdf');
        }else if ($type === 'kategori'){
            $kategoriSeni = KategoriSeni::whereNull('deleted_at')
            ->get(['nama_kategori', 'deskripsi_kategori', 'created_at']);

            if ($kategoriSeni->isEmpty()) {
                return response()->json(['message' => 'Data Kategori Seni tidak ditemukan'], 404);
            }

            $data = ['kategoriSeni' => $kategoriSeni];
            $pdf = PDF::loadView('pdf.kategori_seni_data', $data);

            return $pdf->download('data_kategori_seni.pdf');
        }else if($type === 'seniman'){
            $senimanData = Seniman::with('tingkatan')
            ->whereNull('deleted_at')
            ->get(['nama_seniman', 'tingkatan_id', 'tgl_lahir', 'deskripsi_seniman', 'alamat_seniman', 'noTelp_seniman', 'lama_pengalaman']);

            $data = [
                'senimanData' => $senimanData,
            ];

            $pdf = PDF::loadView('pdf.seniman_data', $data)->setPaper('a4', 'landscape');

            return $pdf->download('seniman_data.pdf');

        } else if($type === 'penilaian'){
            $penilaianData = PenilaianKarya::with([
                'kuota.penilai',
                'rubrikPenilaians',
                'registrasiIndividu',
                'registrasiKelompok',
                'tingkatan'
            ])
            ->whereNull('deleted_at')
            ->get();

            if ($penilaianData->isEmpty()) {
                return response()->json(['message' => 'Data Penilaian Karya tidak ditemukan'], 404);
            }

            $data = $penilaianData->map(function ($penilaian) {
                return [
                    'nama_penilai' => optional($penilaian->kuota->penilai)->nama_penilai ?? 'N/A',
                    'nama' => $penilaian->registrasiIndividu->nama ?? $penilaian->registrasiKelompok->nama_kelompok ?? 'N/A',
                    'tingkatan' => optional($penilaian->tingkatan)->nama_tingkatan ?? 'N/A',
                    'total_nilai' => $penilaian->total_nilai,
                    'komentar' => $penilaian->komentar,
                    'created_at' => $penilaian->created_at->format('Y-m-d'),
                    'rubrik_penilaian' => $penilaian->rubrikPenilaians->map(function ($rubrik) {
                        return [
                            'nama_rubrik' => $rubrik->nama_rubrik,
                            'skor' => $rubrik->pivot->skor,
                        ];
                    }),
                ];
            });

            $pdf = PDF::loadView('pdf.penilaian_data', ['penilaianData' => $data])
                ->setPaper('a4', 'portrait');

            return $pdf->download('penilaian_data.pdf');

        } else if ($type === 'penilai') {
            $penilaiData = Penilai::with('kategoriSeni')
                ->whereNull('deleted_at')
                ->get(['nama_penilai', 'kategori_id', 'tgl_lahir', 'alamat_penilai', 'noTelp_penilai', 'bidang_ahli', 'lembaga', 'kuota']);

            $data = $penilaiData->map(function ($penilai) {
                return [
                    'nama_penilai' => $penilai->nama_penilai,
                    'nama_kategori' => optional($penilai->kategoriSeni)->nama_kategori,
                    'tgl_lahir' => $penilai->tgl_lahir,
                    'alamat_penilai' => $penilai->alamat_penilai,
                    'noTelp_penilai' => $penilai->noTelp_penilai,
                    'bidang_ahli' => $penilai->bidang_ahli,
                    'lembaga' => $penilai->lembaga,
                    'kuota' => $penilai->kuota,
                ];
            });

            $pdf = PDF::loadView('pdf.penilai_data', ['penilaiData' => $data])
                ->setPaper('a4', 'landscape');

            return $pdf->download('penilai_data.pdf');
        }else{
            return response()->json(['message' => 'Parameter type tidak valid'], 400);
        }

    }

}
