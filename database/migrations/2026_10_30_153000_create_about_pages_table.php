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
        if (! Schema::hasTable('about_pages')) {
            Schema::create('about_pages', function (Blueprint $table) {
                $table->id();
                $table->string('badge')->nullable();
                $table->string('title')->nullable();
                $table->text('tagline')->nullable();
                $table->string('storyTitle')->nullable();
                $table->longText('storyContent')->nullable();
                $table->string('heroImage', 1024)->nullable();
                $table->string('heroImageAlt', 255)->nullable();
                $table->string('storyImage', 1024)->nullable();
                $table->string('storyImageAlt', 255)->nullable();
                $table->text('mission')->nullable();
                $table->text('vision')->nullable();
                $table->longText('values')->nullable();
                $table->longText('milestones')->nullable();
                $table->longText('stats')->nullable();
                $table->longText('images')->nullable();
                $table->boolean('teamSectionEnabled')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_pages');
    }
};

