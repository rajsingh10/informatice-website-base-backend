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
        if (! Schema::hasTable('trust_sections')) {
            Schema::create('trust_sections', function (Blueprint $table) {
                $table->id();
                $table->string('eyebrow')->nullable()->default('');
                $table->string('title')->default('Trusted by Industry Leaders');
                $table->text('description')->nullable();
                $table->string('buttonText')->nullable()->default('');
                $table->string('buttonUrl')->nullable()->default('');
                $table->text('backgroundImage')->nullable();
                $table->json('clients')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_sections');
    }
};

