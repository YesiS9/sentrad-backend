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
        Schema::create('anggota_forums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('anggota_id')->references('id')->on('seniman')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('forum_id')->references('id')->on('forums')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('tgl_join');
            $table->string('role');
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
        Schema::dropIfExists('anggota_forums');
    }
};
