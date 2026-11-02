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
        Schema::table('about_pages', function (Blueprint $table) {
            $table->boolean('differentiatorsFeatured')->default(true)->after('teamSectionEnabled');
            $table->boolean('storyFeatured')->default(true)->after('differentiatorsFeatured');
            $table->boolean('valuesFeatured')->default(true)->after('storyFeatured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_pages', function (Blueprint $table) {
            $table->dropColumn([
                'differentiatorsFeatured',
                'storyFeatured',
                'valuesFeatured',
            ]);
        });
    }
};
