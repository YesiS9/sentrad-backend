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
        Schema::create('riwayat_regis_individus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('individu_id')->references('id')->on('registrasi_individus')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama', 100);
            $table->date('tgl_lahir');
            $table->date('tgl_mulai');
            $table->string('alamat');
            $table->string('noTelp', 20);
            $table->string('email');
            $table->string('status_individu');
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
        Schema::dropIfExists('riwayat_regis_individus');
    }
};
