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
        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->ulid('user_id')->index()->after('id');
        });

        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->ulid('user_id')->nullable()->index()->after('id');
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn(['user_id']);
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->ulid('user_id')->nullable()->index()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->dropColumn(['user_id']);
        });

        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['user_id']);
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn(['user_id']);
        });
    }
};
