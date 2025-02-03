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
        Schema::create('registrasi_individus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('seniman_id')->references('id')->on('seniman')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('kategori_id')->references('id')->on('kategori_senis')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama', 100);
            $table->date('tgl_lahir');
            $table->date('tgl_mulai');
            $table->string('alamat');
            $table->string('noTelp', 20);
            $table->string('email');
            $table->string('status_individu');
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
