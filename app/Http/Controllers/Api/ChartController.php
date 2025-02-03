<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use App\Models\User;
use App\Models\RegistrasiIndividu;
use App\Models\RegistrasiKelompok;

class ChartController extends Controller
{
    public function userCountChart()
    {
        $senimanCount = User::whereHas('roles', function ($query) {
            $query->where('roles.nama_role', 'seniman');
        })->whereNull('deleted_at')->count();

        $penilaiCount = User::whereHas('roles', function ($query) {
            $query->where('roles.nama_role', 'penilai');
        })->whereNull('deleted_at')->count();

        $chart = (new LarapexChart)->barChart()
            ->setTitle('User Count by Role')
            ->setXAxis(['Seniman', 'Penilai'])
            ->setDataset([
                [
                    'name' => 'User Count',
                    'data' => [$senimanCount, $penilaiCount]
                ]
            ]);

        $chartData = json_decode($chart->toJson(), true);

        $chartData['options']['legend']['show'] = true;
        if (!isset($chartData['series']) || empty($chartData['series'])) {
            $chartData['series'] = [
                [
                    'name' => 'User Count',
                    'data' => [$senimanCount, $penilaiCount]
                ]
            ];
        }

        return response()->json($chartData);
    }

    public function registrasiCountChart()
    {

        $individuCount = RegistrasiIndividu::whereNull('deleted_at')->count();

        $kelompokCount = RegistrasiKelompok::whereNull('deleted_at')->count();

        $chart = (new LarapexChart)->barChart()
            ->setTitle('Registrasi Count by Type')
            ->setXAxis(['Registrasi Individu', 'Registrasi Kelompok'])
            ->setDataset([
                [
                    'name' => 'Registrasi Count',
                    'data' => [$individuCount, $kelompokCount]
                ]
            ]);

        $chartData = json_decode($chart->toJson(), true);

        $chartData['options']['legend']['show'] = true;
        if (!isset($chartData['series']) || empty($chartData['series'])) {
            $chartData['series'] = [
                [
                    'name' => 'Registrasi Count',
                    'data' => [$individuCount, $kelompokCount]
                ]
            ];
        }

        return response()->json($chartData);
    }



}
