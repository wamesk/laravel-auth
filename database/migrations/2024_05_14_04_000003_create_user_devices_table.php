<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignIdFor(config('wame-auth.model'))->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 100)->nullable();
            $table->json('data')->nullable();
            $table->string('device_token')->nullable();
            $table->string('version', 10)->nullable();
            $table->dateTimeTz('last_login')->nullable();
            $table->dateTimeTz('created_at')->nullable();
            $table->dateTimeTz('updated_at')->nullable();
            $table->dateTimeTz('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
