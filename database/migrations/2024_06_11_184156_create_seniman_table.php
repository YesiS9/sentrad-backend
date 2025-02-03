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
        Schema::create('seniman', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('tingkatan_id')->nullable()->references('id')->on('tingkatans')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nama_seniman', 100);
            $table->date('tgl_lahir');
            $table->text('deskripsi_seniman');
            $table->string('alamat_seniman');
            $table->string('noTelp_seniman', 20);
            $table->integer('lama_pengalaman');
            $table->boolean('status_seniman');
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
        Schema::dropIfExists('seniman');
    }
};
