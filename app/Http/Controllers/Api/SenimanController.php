<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seniman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SenimanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $seniman = Seniman::whereNull('deleted_at')->paginate($perPage);

            if (count($seniman) > 0) {
                Log::info('Data Seniman Berhasil Ditampilkan');
                return response()->json([
                    'data' => $seniman->items(),
                    'current_page' => $seniman->currentPage(),
                    'per_page' => $seniman->perPage(),
                    'total' => $seniman->total(),
                    'last_page' => $seniman->lastPage(),
                    'status' => 'success',
                    'message' => 'Data Seniman Berhasil Ditampilkan',
                ], 200);
            }

            Log::info('Data Seniman Kosong');
            return response()->json([
                'data' => null,
                'status' => 'success',
                'message' => 'Data Seniman Kosong',
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

    public function storeByAdmin(Request $request)
    {
        try {
            $storeData = $request->all();

            $messages = [
                'username.required' => 'Username wajib diisi.',
                'username.exists' => 'Username tidak ditemukan.',
                'nama_seniman.required' => 'Nama Seniman wajib diisi.',
                'nama_seniman.string' => 'Nama Seniman harus berupa teks.',
                'nama_seniman.max' => 'Nama Seniman tidak boleh lebih dari 100 karakter.',
                'tgl_lahir.required' => 'Tanggal Lahir wajib diisi.',
                'tgl_lahir.date_format' => 'Tanggal Lahir harus dalam format Y-m-d.',
                'deskripsi_seniman.required' => 'Deskripsi Seniman wajib diisi.',
                'deskripsi_seniman.string' => 'Deskripsi Seniman harus berupa teks.',
                'alamat_seniman.required' => 'Alamat Seniman wajib diisi.',
                'alamat_seniman.string' => 'Alamat Seniman harus berupa teks.',
                'noTelp_seniman.required' => 'No. Telp Seniman wajib diisi.',
                'noTelp_seniman.regex' => 'Nomor telepon harus diawali dengan 08 dan memiliki panjang antara 10 hingga 14 digit.',
                'lama_pengalaman.required' => 'Lama Pengalaman wajib diisi.',
                'lama_pengalaman.integer' => 'Lama Pengalaman harus berupa angka.',
                'lama_pengalaman.min' => 'Lama Pengalaman tidak boleh kurang dari 0.',

            ];
            $validate = Validator::make($storeData, [
                'username' => 'required|string|exists:users,username',
                'nama_seniman' => 'required|string|max:100',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'deskripsi_seniman' => 'required|string',
                'alamat_seniman' => 'required|string',
                'noTelp_seniman' => 'required|regex:/^08\d{8,12}$/',
                'lama_pengalaman' => 'required|integer|min:0',
                'status_seniman' => 'required|boolean',
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
            if (!$user) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }


            $existingSeniman = Seniman::where('user_id', $user->id)->first();
            if ($existingSeniman) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'User telah memiliki data seniman',
                ], 400);
            }

            $storeData['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_lahir'])->format('Y-m-d');
            $storeData['user_id'] = $user->id;
            unset($storeData['username']);


            $seniman = Seniman::create($storeData);

            Log::info('Data Seniman Berhasil Ditambahkan');
            return response()->json([
                'data' => $seniman,
                'status' => 'success',
                'message' => 'Data Seniman Berhasil Ditambahkan',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $messages = [
                'username.required' => 'Username wajib diisi.',
                'username.exists' => 'Username tidak ditemukan.',
                'nama_seniman.required' => 'Nama Seniman wajib diisi.',
                'nama_seniman.string' => 'Nama Seniman harus berupa teks.',
                'nama_seniman.max' => 'Nama Seniman tidak boleh lebih dari 100 karakter.',
                'tgl_lahir.required' => 'Tanggal Lahir wajib diisi.',
                'tgl_lahir.date_format' => 'Tanggal Lahir harus dalam format Y-m-d.',
                'deskripsi_seniman.required' => 'Deskripsi Seniman wajib diisi.',
                'deskripsi_seniman.string' => 'Deskripsi Seniman harus berupa teks.',
                'alamat_seniman.required' => 'Alamat Seniman wajib diisi.',
                'alamat_seniman.string' => 'Alamat Seniman harus berupa teks.',
                'noTelp_seniman.required' => 'No. Telp Seniman wajib diisi.',
                'noTelp_seniman.regex' => 'Nomor telepon harus diawali dengan 08 dan memiliki panjang antara 10 hingga 14 digit.',
                'lama_pengalaman.required' => 'Lama Pengalaman wajib diisi.',
                'lama_pengalaman.integer' => 'Lama Pengalaman harus berupa angka.',
                'lama_pengalaman.min' => 'Lama Pengalaman tidak boleh kurang dari 0.',

            ];
            $validate = Validator::make($storeData, [
                'user_id' => 'required|string|exists:users,id',
                'nama_seniman' => 'required|string',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'deskripsi_seniman' => 'required|string',
                'alamat_seniman' => 'required|string',
                'noTelp_seniman' => 'required|regex:/^08\d{8,12}$/',
                'lama_pengalaman' => 'required|integer',
                'status_seniman' => 'required|boolean',
            ],$messages);


            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }

            $storeData['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $storeData['tgl_lahir'])->format('Y-m-d');


            $seniman = Seniman::create($storeData);

            Log::info('Data Seniman Berhasil Ditambahkan');
            return response()->json([
                'data' => $seniman,
                'status' => 'success',
                'message' => 'Data Seniman Berhasil Ditambahkan',
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
        $seniman = Seniman::whereNull('deleted_at')
            ->with('user')
            ->with('tingkatan')
            ->find($id);

        if (!$seniman) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Data Seniman tidak ditemukan',
            ], 404);
        }


        $responseData = $seniman->toArray();
        $responseData['username'] = $seniman->user->username;

        return response()->json([
            'data' => $responseData,
            'status' => 'success',
            'message' => 'Data Seniman Berhasil Ditampilkan',
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

            $updateData = $request->all();

            $messages = [
                'username.required' => 'Username wajib diisi.',
                'username.exists' => 'Username tidak ditemukan.',
                'nama_seniman.required' => 'Nama Seniman wajib diisi.',
                'nama_seniman.string' => 'Nama Seniman harus berupa teks.',
                'nama_seniman.max' => 'Nama Seniman tidak boleh lebih dari 100 karakter.',
                'tgl_lahir.required' => 'Tanggal Lahir wajib diisi.',
                'tgl_lahir.date_format' => 'Tanggal Lahir harus dalam format Y-m-d.',
                'deskripsi_seniman.required' => 'Deskripsi Seniman wajib diisi.',
                'deskripsi_seniman.string' => 'Deskripsi Seniman harus berupa teks.',
                'alamat_seniman.required' => 'Alamat Seniman wajib diisi.',
                'alamat_seniman.string' => 'Alamat Seniman harus berupa teks.',
                'noTelp_seniman.required' => 'No. Telp Seniman wajib diisi.',
                'noTelp_seniman.regex' => 'Nomor telepon harus diawali dengan 08 dan memiliki panjang antara 10 hingga 14 digit.',
                'lama_pengalaman.required' => 'Lama Pengalaman wajib diisi.',
                'lama_pengalaman.integer' => 'Lama Pengalaman harus berupa angka.',
                'lama_pengalaman.min' => 'Lama Pengalaman tidak boleh kurang dari 0.',

            ];
            $validate = Validator::make($updateData, [
                'username' => 'required|string|exists:users,username',
                'nama_seniman' => 'required|string|max:100',
                'tgl_lahir' => 'required|date_format:d/m/Y',
                'deskripsi_seniman' => 'required|string',
                'alamat_seniman' => 'required|string',
                'noTelp_seniman' => 'required||regex:/^08\d{8,12}$/',
                'lama_pengalaman' => 'required|integer|min:0',
                'status_seniman' => 'required|boolean',
            ], $messages);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors()->first(),
                ], 400);
            }

            $user = User::where('username', $updateData['username'])->first();
            if (!$user) {
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }

            $seniman = Seniman::where('id', $id)->where('user_id', $user->id)->first();
            if (!$seniman) {
                Log::error('Data Seniman Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Seniman Tidak Ditemukan',
                ], 404);
            }


            $updateData['tgl_lahir'] = Carbon::createFromFormat('d/m/Y', $updateData['tgl_lahir'])->format('Y-m-d');
            unset($updateData['username']);
            $seniman->update($updateData);

            Log::info('Data Seniman Berhasil Diupdate');
            return response()->json([
                'data' => $seniman,
                'status' => 'success',
                'message' => 'Data Seniman Berhasil Diupdate',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception Error: ' . $e->getMessage());
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $seniman = Seniman::whereNull('deleted_at')->find($id);

            if (!$seniman) {
                Log::error('Data Seniman Tidak Ditemukan');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Data Seniman Tidak Ditemukan',
                ], 404);
            }

            if ($seniman->delete()) {
                Log::info('Data Seniman Berhasil Dihapus');
                return response()->json([
                    'data' => $seniman,
                    'status' => 'success',
                    'message' => 'Data Seniman Berhasil Dihapus',
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

    public function getTotalSeniman()
    {
        $totalSeniman = Seniman::whereNull('deleted_at')->count();

        return response()->json(['total_senimans' => $totalSeniman]);
    }
}
