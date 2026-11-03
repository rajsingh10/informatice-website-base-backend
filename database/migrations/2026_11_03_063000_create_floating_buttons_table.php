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
        if (! Schema::hasTable('floating_buttons')) {
            Schema::create('floating_buttons', function (Blueprint $table) {
                $table->id();
                $table->boolean('enabled')->default(true);
                $table->string('buttonText', 150)->default('Contact us');
                $table->string('buttonLink', 1024)->default('/contact');
                $table->string('buttonColor', 30)->default('#2563eb');
                $table->string('textColor', 30)->default('#ffffff');
                $table->string('position', 30)->default('bottom-right');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floating_buttons');
    }
};

