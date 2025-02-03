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
        Schema::create('registrasi_kelompoks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('seniman_id')->references('id')->on('seniman')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('kategori_id')->references('id')->on('kategori_senis')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama_kelompok', 100);
            $table->date('tgl_terbentuk');
            $table->string('alamat_kelompok');
            $table->text('deskripsi_kelompok');
            $table->string('noTelp_kelompok', 20);
            $table->string('email_kelompok');
            $table->integer('jumlah_anggota');
            $table->string('status_kelompok');
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
        Schema::dropIfExists('registrasi_individus');
    }
};
