<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\KategoriSeni;
use App\Models\AnggotaForum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function index()
    {
        try {
            $forum = Forum::whereNull('deleted_at')
                ->withCount('anggotaForums')
                ->get();

            if (count($forum) > 0) {
                Log::info('Data Forum Berhasil Ditampilkan');
                return response()->json([
                    'data' => $forum,
                    'status' => 'success',
                    'message' => 'Data Forum Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Forum Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Forum Kosong',
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


    public function indexFollowForum()
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
            $forums = Forum::whereNull('deleted_at')
                ->whereHas('anggotaForums', function($query) use ($senimanId) {
                    $query->where('anggota_id', $senimanId)
                        ->where('role', 'anggota');
                })
                ->withCount('anggotaForums')
                ->get();

            if ($forums->count() > 0) {
                Log::info('Data Forum Aktif Berhasil Ditampilkan');
                return response()->json([
                    'data' => $forums,
                    'status' => 'success',
                    'message' => 'Data Forum Aktif Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Forum Aktif Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Forum Aktif Kosong',
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

    public function indexByUser(Request $request)
    {
        try {
            $seniman_id = $request->input('seniman_id');

            if (!$seniman_id) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Seniman ID tidak ditemukan',
                ], 400);
            }

            $forums = Forum::where('seniman_id', $seniman_id)
                ->whereNull('deleted_at')
                ->withCount('anggotaForums')
                ->get();

            if ($forums->count() > 0) {
                Log::info('Data Forum untuk Seniman dengan ID ' . $seniman_id . ' Berhasil Ditampilkan');
                return response()->json([
                    'seniman_id' => $seniman_id,
                    'data' => $forums,
                    'status' => 'success',
                    'message' => 'Data Forum untuk Seniman Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Forum untuk Seniman dengan ID ' . $seniman_id . ' Kosong');
            return response()->json([
                'seniman_id' => $seniman_id,
                'data' => null,
                'status' => 'success',
                'message' => 'Data Forum Kosong',
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




    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.exists' => 'Nama kategori tidak ditemukan dalam data kategori seni.',
                'judul_forum.required' => 'Judul forum wajib diisi.',
                'judul_forum.string' => 'Judul forum harus berupa teks.',
                'judul_forum.max' => 'Judul forum tidak boleh lebih dari 100 karakter.',
                'status_forum.required' => 'Status forum wajib diisi.',
            ];
            $validate = Validator::make($storeData, [
                'seniman_id' => 'required|exists:seniman,id',
                'nama_kategori' => 'required|string|exists:kategori_senis,nama_kategori',
                'judul_forum' => 'required|string|max:100',
                'status_forum' => 'required|boolean',
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
            $forum = Forum::create($storeData);

            AnggotaForum::create([
                'anggota_id' => $storeData['seniman_id'],
                'forum_id' => $forum->id,
                'tgl_join' => now(),
                'role' => 'Pemilik',
            ]);

            Log::info('Data Forum Berhasil Ditambahkan dan Seniman ditambahkan sebagai anggota forum');
            return response()->json([
                'data' => $forum,
                'status' => 'success',
                'message' => 'Data Forum Berhasil Ditambahkan dan Seniman menjadi anggota forum',
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


    public function update(Request $request, $id)
    {
        try {
            $forum = Forum::whereNull('deleted_at')->find($id);

            if (!$forum) {
                Log::error('Data Forum Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Forum Tidak Ditemukan',
                ], 404);
            }
            $messages = [
                'nama_kategori.required' => 'Nama kategori wajib diisi.',
                'nama_kategori.exists' => 'Nama kategori tidak ditemukan dalam data kategori seni.',
                'judul_forum.required' => 'Judul forum wajib diisi.',
                'judul_forum.string' => 'Judul forum harus berupa teks.',
                'judul_forum.max' => 'Judul forum tidak boleh lebih dari 100 karakter.',
                'status_forum.required' => 'Status forum wajib diisi.',
            ];
            $validate = Validator::make($request->all(), [
                'seniman_id' => 'required|exists:seniman,id',
                'nama_kategori' => 'required|string|exists:kategori_senis,nama_kategori',
                'judul_forum' => 'required|string|max:100',
                'status_forum' => 'required|boolean',
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

            $forum->seniman_id = $request->seniman_id;
            $forum->kategori_id = $kategori->id;
            $forum->judul_forum = $request->judul_forum;
            $forum->status_forum = $request->status_forum;

            $forum->save();

            Log::info('Data Forum Berhasil Diupdate');
            return response()->json([
                'data' => $forum,
                'status' => 'success',
                'message' => 'Data Forum Berhasil Diupdate',
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


    public function show($id){
        try {
            $forum = Forum::whereNull('deleted_at')
            ->withCount('anggotaForums')
            ->find($id);

            if (!$forum) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Forum tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $forum,
                'status' => 'success',
                'message' => 'Data Forum Berhasil Ditampilkan',
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
            $forum = Forum::whereNull('deleted_at')->find($id);

            if (!$forum) {
                Log::error('Data Forum Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Forum Tidak Ditemukan',
                ], 404);
            }

            if ($forum->delete()) {
                Log::info('Data Forum Berhasil Dihapus');
                return response()->json([
                    'data' => $forum,
                    'status' => 'success',
                    'message' => 'Data Forum Berhasil Dihapus',
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
