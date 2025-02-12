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
        Schema::create('rubriks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penilai_id')->references('id')->on('penilais')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama_rubrik', 100);
            $table->text('deskripsi_rubrik');
            $table->integer('bobot');
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
        Schema::dropIfExists('rubriks');
    }
};
