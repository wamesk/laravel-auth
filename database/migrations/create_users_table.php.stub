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
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->integer('sort_order')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('socialite_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->integer('sort_order')->nullable();
            $table->ulid('user_id');
            $table->string('socialite_provider_id');
            $table->string('provider_user_id');
            $table->string('provider_user_token')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('socialite_accounts');
    }
};
