<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('type', 50)->default('full-time'); // full-time, part-time, contract, internship
            $table->string('experience', 100)->nullable();
            $table->longText('description')->nullable();
            $table->json('requirements')->nullable();
            $table->string('applyUrl', 1000)->nullable();
            $table->string('status', 20)->default('open'); // open, closed
            $table->integer('order')->default(0);
            $table->timestamp('postedAt')->nullable();
            $table->timestamps();

            $table->index(['status', 'order']);
            $table->index('department');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
