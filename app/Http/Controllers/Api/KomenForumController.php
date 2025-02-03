<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KomenForum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KomenForumController extends Controller
{
    public function index(Request $request)
    {
        try {
            $forumId = $request->input('forum_id');

            if (!$forumId) {
                return response()->json([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'Forum ID tidak diberikan',
                ], 400);
            }

            $komenForum = KomenForum::where('forum_id', $forumId)
                ->select('id', 'forum_id', 'seniman_id','isi_komenForum', 'waktu_komenForum', 'created_at')
                ->paginate(10);

            if ($komenForum->count() > 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => $komenForum->items(),
                    'current_page' => $komenForum->currentPage(),
                    'last_page' => $komenForum->lastPage(),
                    'per_page' => $komenForum->perPage(),
                    'total' => $komenForum->total(),
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Komentar forum tidak ditemukan',
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
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'seniman_id' => 'required|string',
                'forum_id' => 'required|string',
                'isi_komenForum' => 'required|string',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }
            $storeData['waktu_komenForum'] = now();
            $komenForum = KomenForum::create($storeData);


            Log::info('Komentar Forum Berhasil Ditambahkan');
            return response()->json([
                'data' => $komenForum,
                'status' => 'success',
                'message' => 'Komentar Forum Berhasil Ditambahkan',
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
            $komenForum = KomenForum::whereNull('deleted_at')->find($id);

            if (!$komenForum) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar Forum tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'data' => $komenForum,
                'status' => 'success',
                'message' => 'Komentar Forum Berhasil Ditampilkan',
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
            $komenForum = KomenForum::whereNull('deleted_at')->find($id);
            if (!$komenForum) {
                Log::error('Komentar tidak ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar tidak ditemukan',
                ], 404);
            }

            $validate = Validator::make($request->all(), [
                'isi_komenForum' => 'required|string',
            ]);
            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $komenForum->update([
                'isi_komenForum' => $request->isi_komenForum,
            ]);

            $komenForum->waktu_komenForum = $komenForum->updated_at;
            $komenForum->save();

            Log::info('Komentar Forum Berhasil Diupdate');
            return response()->json([
                'data' => $komenForum,
                'status' => 'success',
                'message' => 'Komentar Forum Berhasil Diupdate',
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
            $komenForum = KomenForum::whereNull('deleted_at')->find($id);

            if (!$komenForum) {
                Log::error('Komentar Forum Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Komentar Forum Tidak Ditemukan',
                ], 404);
            }

            if ($komenForum->delete()) {
                Log::info('Komentar Forum Berhasil Dihapus');
                return response()->json([
                    'data' => $komenForum,
                    'status' => 'success',
                    'message' => 'Komentar Forum Berhasil Dihapus',
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
