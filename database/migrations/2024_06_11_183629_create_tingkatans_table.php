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
        Schema::create('tingkatans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_tingkatan', 100);
            $table->text('deskripsi_tingkatan');
            $table->integer('nilai_min');
            $table->integer('nilai_max');
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
        Schema::dropIfExists('tingkatans');
    }
};
