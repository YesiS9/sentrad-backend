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
        Schema::create('komen_proyeks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('proyek_id')->references('id')->on('proyeks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('seniman_id')->references('id')->on('seniman')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('isi_komenProyek');
            $table->datetime('waktu_komenProyek');
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
        Schema::dropIfExists('komen_proyeks');
    }
};
