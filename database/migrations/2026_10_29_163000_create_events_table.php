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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 100)->default('Conference')->index();
            $table->text('shortDescription')->nullable();
            $table->longText('longDescription')->nullable();
            $table->date('date')->index();
            $table->date('endDate')->nullable()->index();
            $table->string('startTime', 50)->nullable();
            $table->string('endTime', 50)->nullable();
            $table->string('venue', 500)->nullable();
            $table->boolean('isOnline')->default(false)->index();
            $table->string('onlineUrl', 1000)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('totalSeats')->default(0);
            $table->unsignedInteger('availableSeats')->default(0);
            $table->json('agenda')->nullable();
            $table->string('image', 1000)->nullable();
            $table->string('status', 30)->default('published')->index();
            $table->json('seo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

