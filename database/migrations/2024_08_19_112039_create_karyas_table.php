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
        Schema::create('karyas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('portofolio_id')->references('id')->on('portofolios')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('judul_karya', 100);
            $table->date('tgl_pembuatan');
            $table->text('deskripsi_karya');
            $table->string('bentuk_karya');
            $table->string('media_karya');
            $table->string('status_karya');
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
        Schema::dropIfExists('karyas');
    }
};
