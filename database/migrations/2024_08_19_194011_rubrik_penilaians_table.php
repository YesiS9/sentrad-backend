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
        Schema::create('rubrik_penilaians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('rubrik_id')->references('id')->on('rubriks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('penilaian_karya_id')->references('id')->on('penilaian_karyas')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('skor');
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
        Schema::dropIfExists('rubrik_penilaians');
    }
};
