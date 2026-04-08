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
        Schema::create('oauth_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->uuid('license_id');
            $table->string('access_token')->nullable();
            $table->boolean('used')->default(false);
            $table->timestamps();

            $table->foreign('license_id')->references('id')->on('licenses')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_codes');
    }
};
