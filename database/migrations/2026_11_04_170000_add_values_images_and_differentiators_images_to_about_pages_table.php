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
        if (Schema::hasTable('about_pages')) {
            Schema::table('about_pages', function (Blueprint $table) {
                if (! Schema::hasColumn('about_pages', 'valuesImages')) {
                    $table->longText('valuesImages')->nullable()->after('images');
                }
                if (! Schema::hasColumn('about_pages', 'differentiatorsImages')) {
                    $table->longText('differentiatorsImages')->nullable()->after('valuesImages');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('about_pages')) {
            Schema::table('about_pages', function (Blueprint $table) {
                if (Schema::hasColumn('about_pages', 'differentiatorsImages')) {
                    $table->dropColumn('differentiatorsImages');
                }
                if (Schema::hasColumn('about_pages', 'valuesImages')) {
                    $table->dropColumn('valuesImages');
                }
            });
        }
    }
};

