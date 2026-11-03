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
        if (! Schema::hasTable('stats')) {
            Schema::create('stats', function (Blueprint $table) {
                $table->id();
                $table->double('number')->default(0);
                $table->string('suffix', 20)->nullable()->default('+');
                $table->string('label', 150);
                $table->string('icon', 100)->nullable()->default('Briefcase');
                $table->text('description')->nullable();
                $table->boolean('isFeatured')->default(true);
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};

