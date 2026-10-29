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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('siteName')->default('Acme');
            $table->string('logoUrl')->nullable()->default('');
            $table->string('faviconUrl')->nullable()->default('');
            $table->string('email')->nullable()->default('');
            $table->string('phone')->nullable()->default('');
            $table->text('address')->nullable();
            $table->text('mapEmbedUrl')->nullable();
            $table->json('offices')->nullable();
            $table->json('socials')->nullable();
            $table->json('announcement')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
