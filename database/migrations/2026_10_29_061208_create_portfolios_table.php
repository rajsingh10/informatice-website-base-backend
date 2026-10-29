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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('client')->nullable()->default('');
            $table->string('year')->nullable()->default('');
            $table->string('category')->nullable()->default('General');
            $table->json('technologies')->nullable();
            $table->text('shortDescription')->nullable();
            $table->longText('longDescription')->nullable();
            $table->text('challenge')->nullable();
            $table->text('solution')->nullable();
            $table->json('results')->nullable();
            $table->string('image', 1024)->nullable()->default('');
            $table->json('gallery')->nullable();
            $table->string('projectUrl', 1024)->nullable()->default('');
            $table->boolean('isFeatured')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('isPublished')->default(true);
            $table->json('seo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
