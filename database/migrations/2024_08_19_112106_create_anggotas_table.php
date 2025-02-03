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
        Schema::create('anggotas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kelompok_id')->references('id')->on('registrasi_kelompoks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama_anggota', 100);
            $table->date('tgl_lahir');
            $table->date('tgl_gabung');
            $table->string('alamat_anggota');
            $table->string('noTelp_anggota', 20);
            $table->string('tingkat_skill', 100);
            $table->string('peran_anggota', 100);
            $table->string('status_anggota');
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
        Schema::dropIfExists('anggotas');
    }
};
