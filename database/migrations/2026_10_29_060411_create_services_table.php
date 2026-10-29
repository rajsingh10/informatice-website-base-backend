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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable()->default('General');
            $table->text('shortDescription')->nullable();
            $table->longText('fullDescription')->nullable();
            $table->string('icon')->nullable();
            $table->string('image', 1024)->nullable();
            $table->json('features')->nullable();
            $table->json('deliverables')->nullable();
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
        Schema::dropIfExists('services');
    }
};
