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
        // Drop legacy team_members pivot table if present from previous template
        Schema::dropIfExists('team_members');

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation');
            $table->string('department')->nullable();
            $table->text('photo')->nullable();
            $table->text('bio')->nullable();
            $table->json('social')->nullable();
            $table->boolean('isLeadership')->default(false);
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
        Schema::dropIfExists('team_members');
    }
};
