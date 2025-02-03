<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riwayat_regis_kelompoks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kelompok_id')->references('id')->on('registrasi_kelompoks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama_kelompok', 100);
            $table->date('tgl_terbentuk');
            $table->string('alamat_kelompok');
            $table->text('deskripsi_kelompok');
            $table->string('noTelp_kelompok', 20);
            $table->string('email_kelompok');
            $table->integer('jumlah_anggota');
            $table->string('status_kelompok');
            $table->string('tingkatan');
            $table->date('tgl_riwayat');
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riwayat_regis_kelompoks');
    }
};
