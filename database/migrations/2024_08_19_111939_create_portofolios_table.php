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
        Schema::create('portofolios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kelompok_id')->nullable()->references('id')->on('registrasi_kelompoks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('seniman_id')->nullable()->references('id')->on('seniman')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('kategori_id')->references('id')->on('kategori_senis')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('judul_portofolio', 100);
            $table->date('tgl_dibuat');
            $table->text('deskripsi_portofolio');
            $table->integer('jumlah_karya');
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
        Schema::dropIfExists('portofolios');
    }
};
