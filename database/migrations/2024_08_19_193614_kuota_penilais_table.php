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
        Schema::create('kuota_penilais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penilai_id')->references('id')->on('penilais')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('periode_bulan');
            $table->integer('kuota_terpakai');
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
        Schema::dropIfExists('kuota_penilais');
    }
};
