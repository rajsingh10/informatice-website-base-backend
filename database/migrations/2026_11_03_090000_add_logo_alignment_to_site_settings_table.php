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
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'logoPosition')) {
                $table->string('logoPosition')->default('left')->after('logoUrl');
            }
            if (! Schema::hasColumn('site_settings', 'logoAlignment')) {
                $table->string('logoAlignment')->default('left')->after('logoPosition');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'logoAlignment')) {
                $table->dropColumn('logoAlignment');
            }
            if (Schema::hasColumn('site_settings', 'logoPosition')) {
                $table->dropColumn('logoPosition');
            }
        });
    }
};

