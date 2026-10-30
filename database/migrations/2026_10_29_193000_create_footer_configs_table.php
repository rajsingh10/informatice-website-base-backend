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
        if (! Schema::hasTable('footer_configs')) {
            Schema::create('footer_configs', function (Blueprint $table) {
                $table->id();
                $table->string('layoutId')->default('layout1');
                $table->json('cta')->nullable();
                $table->json('grid')->nullable();
                $table->json('items')->nullable();
                $table->json('links')->nullable();
                $table->json('customCode')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_configs');
    }
};
