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
        Schema::create('page_seos', function (Blueprint $table) {
            $table->string('page')->primary();
            $table->string('title', 70)->nullable();
            $table->string('description', 160)->nullable();
            $table->text('keywords')->nullable();
            $table->string('ogTitle')->nullable();
            $table->text('ogDescription')->nullable();
            $table->string('ogImage')->nullable();
            $table->string('canonicalUrl')->nullable();
            $table->string('robots')->default('index, follow');
            $table->json('imageAltMap')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_seos');
    }
};
