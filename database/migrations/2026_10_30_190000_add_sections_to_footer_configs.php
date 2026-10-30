<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add sections JSON column to footer_configs and ensure items column exists.
     * sections stores the grid cell assignments in row-major order.
     */
    public function up(): void
    {
        Schema::table('footer_configs', function (Blueprint $table) {
            if (! Schema::hasColumn('footer_configs', 'sections')) {
                $table->json('sections')->nullable()->after('grid');
            }
            if (! Schema::hasColumn('footer_configs', 'items')) {
                $table->json('items')->nullable()->after('links');
            }
        });
    }

    public function down(): void
    {
        Schema::table('footer_configs', function (Blueprint $table) {
            if (Schema::hasColumn('footer_configs', 'sections')) {
                $table->dropColumn('sections');
            }
        });
    }
};

