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
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Headline Title *
            $table->text('subtitle')->nullable(); // Subheadline Description
            $table->text('description')->nullable(); // Extended description alias
            $table->boolean('isFeatured')->default(true); // Featured on homepage checkbox
            $table->string('primaryButtonText')->nullable(); // Primary Button Text
            $table->string('primaryButtonUrl')->nullable(); // Primary Button URL
            $table->string('secondaryButtonText')->nullable(); // Secondary Button Text
            $table->string('secondaryButtonUrl')->nullable(); // Secondary Button URL
            $table->string('image')->nullable(); // Hero Background Media
            $table->string('imageAlt')->nullable(); // Image Alt Text (SEO & Accessibility) *
            $table->integer('order')->default(0);
            $table->boolean('isPublished')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
