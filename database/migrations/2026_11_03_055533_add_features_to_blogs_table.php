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
        Schema::table('blogs', function (Blueprint $table) {
            $table->json('features')->nullable()->after('content');
            $table->json('sections')->nullable()->after('features');
            $table->json('keyTakeaways')->nullable()->after('sections');
            $table->text('conclusion')->nullable()->after('keyTakeaways');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn([
                'features',
                'sections',
                'keyTakeaways',
                'conclusion',
            ]);
        });
    }
};
