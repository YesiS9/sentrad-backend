<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RegistrasiIndividu;
use App\Models\RegistrasiKelompok;
use App\Models\RiwayatRegisIndividu;
use App\Models\RiwayatRegisKelompok;
use Carbon\Carbon;

class DeleteOldRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registrations:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move old registrations to history tables and delete them from the main tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->createRiwayatIndividu();
        $this->createRiwayatKelompok();

        $this->info('Proses memindahkan data ke tabel riwayat dan menghapus data lama selesai.');

        return Command::SUCCESS;
    }

    /**
     * Create history for old individual registrations and delete them from the original table.
     */
    private function createRiwayatIndividu()
    {
        $oneYearAgo = Carbon::now()->subYear();
        $individu = RegistrasiIndividu::where('created_at', '<', $oneYearAgo)->get();

        foreach ($individu as $data) {
            RiwayatRegisIndividu::create([
                'individu_id' => $data->id,
                'nama' => $data->nama,
                'tgl_lahir' => $data->tgl_lahir,
                'tgl_mulai' => $data->tgl_mulai,
                'alamat' => $data->alamat,
                'email' => $data->email,
                'noTelp' => $data->noTelp,
                'alamat' => $data->alamat,
                'status_individu' => $data->status_individu,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ]);
            $data->delete();
        }

        $this->info('Data registrasi individu yang lebih dari satu tahun telah dipindahkan.');
    }

    /**
     * Create history for old group registrations and delete them from the original table.
     */
    private function createRiwayatKelompok()
    {
        $oneYearAgo = Carbon::now()->subYear();

        // Ambil data registrasi kelompok lebih dari satu tahun
        $kelompok = RegistrasiKelompok::where('created_at', '<', $oneYearAgo)->get();

        // Pindahkan data ke tabel riwayat dan hapus dari tabel utama
        foreach ($kelompok as $data) {
            RiwayatRegisKelompok::create([
                'registrasi_id' => $data->id,
                'seniman_id' => $data->seniman_id,
                'kategori_id' => $data->kategori_id,
                'nama_kelompok' => $data->nama_kelompok,
                'tgl_terbentuk' => $data->tgl_terbentuk,
                'alamat_kelompok' => $data->alamat_kelompok,
                'deskripsi_kelompok' => $data->deskripsi_kelompok,
                'noTelp_kelompok' => $data->noTelp_kelompok,
                'email_kelompok' => $data->email_kelompok,
                'jumlah_anggota' => $data->jumlah_anggota,
                'status_kelompok' => $data->status_kelompok,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ]);
            $data->delete();
        }

        $this->info('Data registrasi kelompok yang lebih dari satu tahun telah dipindahkan.');
    }
}
