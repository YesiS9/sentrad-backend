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
        Schema::create('reply_komens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('komenProyek_id')->references('id')->on('komen_proyeks')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('komenForum_id')->references('id')->on('komen_forums')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('seniman_id')->references('id')->on('seniman')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('isi_reply');
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
        Schema::dropIfExists('reply_komens');
    }
};
