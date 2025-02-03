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
        Schema::create('penilais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('kategori_id')->references('id')->on('kategori_senis')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama_penilai', 100);
            $table->string('alamat_penilai');
            $table->string('noTelp_penilai', 20);
            $table->string('bidang_ahli');
            $table->string('lembaga');
            $table->date('tgl_lahir');
            $table->string('status_penilai');
            $table->integer('kuota');
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
        Schema::dropIfExists('penilai');
    }
};
