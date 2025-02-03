<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\RubrikPenilaian;
use App\Models\Rubrik;
use App\Models\PenilaianKarya;
use Illuminate\Support\Facades\Log;

class RubrikPenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $storeData = $request->all();

            $validate = Validator::make($request->all(), [
                'nama_rubrik' => 'required|exists:rubriks,nama_rubrik',
                'penilaian_karya_id' => 'required|exists:penilaian_karyas,id',
            ]);

            if ($validate->fails()) {
                Log::error('Validation error: ' . $validate->errors());
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => $validate->errors(),
                ], 400);
            }


            $rubrik = Rubrik::where('nama_rubrik', $storeData['nama_rubrik'])->first();
            if (!$rubrik) {
                Log::error('Rubrik not found');
                return response()->json([
                    'data' => null,
                    'status' => 'error',
                    'message' => 'Rubrik not found',
                ], 404);
            }

            $storeData['rubrik_id'] = $rubrik->id;

            $rubrikPenilaian = RubrikPenilaian::create($storeData);

            Log::info('Data Rubrik Penilaian Berhasil Ditambahkan');
            return response()->json([
                'data' => $rubrikPenilaian,
                'status' => 'success',
                'message' => 'Data Rubrik Penilaian Berhasil Ditambahkan',
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    }
}
