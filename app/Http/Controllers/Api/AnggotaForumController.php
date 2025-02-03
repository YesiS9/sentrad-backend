<?php

namespace App\Http\Controllers\Api;
use App\Models\AnggotaForum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AnggotaForumController extends Controller
{

    public function index($forum_id)
    {
        try {
            $anggotas = AnggotaForum::where('forum_id', $forum_id)
                        ->whereNull('deleted_at')
                        ->get();

            if (count($anggotas) > 0) {
                Log::info('Data Anggota untuk Forum ID: ' . $forum_id . ' Berhasil Ditampilkan');
                return response()->json([
                    'data' => $anggotas,
                    'status' => 'success',
                    'message' => 'Data Anggota untuk Forum Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Anggota untuk Forum ID: ' . $forum_id . ' Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Anggota untuk Forum Kosong',
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

    public function joinForum(Request $request)
    {
        $request->validate([
            'forum_id' => 'required|exists:forums,id',
            'seniman_id' => 'required|exists:seniman,id',
        ]);

        $anggotaExist = AnggotaForum::where('forum_id', $request->forum_id)
                                    ->where('anggota_id', $request->seniman_id)
                                    ->first();

        if ($anggotaExist) {
            return response()->json(['message' => 'Seniman sudah menjadi anggota forum ini'], 409);
        }

        $anggotaForum = AnggotaForum::create([
            'anggota_id' => $request->seniman_id,
            'forum_id' => $request->forum_id,
            'tgl_join' => now(),
            'role' => 'anggota'
        ]);

        return response()->json([
            'message' => 'Berhasil bergabung dengan forum',
            'data' => $anggotaForum
        ], 201);
    }

    public function destroy($forum_id, $anggota_id)
    {
        try {
            $anggota = AnggotaForum::where('forum_id', $forum_id)
                                    ->where('anggota_id', $anggota_id)
                                    ->whereNull('deleted_at')
                                    ->first();

            if (!$anggota) {
                Log::error('Data Anggota Forum Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Anggota Tidak Ditemukan',
                ], 404);
            }

            if ($anggota->role === 'pemilik') {
                Log::warning('Pemilik Forum Tidak Bisa Keluar, Hanya Bisa Menghapus Forum');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Pemilik forum tidak bisa keluar, hanya bisa menghapus forum.',
                ], 403);
            }

            if ($anggota->delete()) {
                Log::info('Data Anggota Berhasil Dihapus');
                return response()->json([
                    'data' => $anggota,
                    'status' => 'success',
                    'message' => 'Berhasil keluar dari forum',
                ], 200);
            }

        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server.'
            ], 500);
        }
    }


}
