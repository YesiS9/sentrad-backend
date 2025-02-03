<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserIdAndClientIdToUuidInOauthAccessTokensTable extends Migration
{
    public function up()
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            // Drop existing indexes if they exist
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('oauth_access_tokens');

            if (array_key_exists('oauth_access_tokens_user_id_index', $indexes)) {
                $table->dropIndex('oauth_access_tokens_user_id_index');
            }
            if (array_key_exists('oauth_access_tokens_client_id_index', $indexes)) {
                $table->dropIndex('oauth_access_tokens_client_id_index');
            }

            // Ubah kolom user_id dan client_id menjadi UUID
            $table->uuid('user_id')->nullable()->change();
            $table->uuid('client_id')->change();

            // Add indexes back
            $table->index('user_id');
            $table->index('client_id');
        });
    }

    public function down()
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            // Drop existing indexes if they exist
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('oauth_access_tokens');

            if (array_key_exists('oauth_access_tokens_user_id_index', $indexes)) {
                $table->dropIndex('oauth_access_tokens_user_id_index');
            }
            if (array_key_exists('oauth_access_tokens_client_id_index', $indexes)) {
                $table->dropIndex('oauth_access_tokens_client_id_index');
            }

            // Kembalikan tipe data user_id dan client_id ke tipe data sebelumnya
            $table->integer('user_id')->change();
            $table->integer('client_id')->change();

            // Add indexes back
            $table->index('user_id');
            $table->index('client_id');
        });
    }
}
