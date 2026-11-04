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
        Schema::table('footer_configs', function (Blueprint $table) {
            if (! Schema::hasColumn('footer_configs', 'bottom')) {
                $table->json('bottom')->nullable()->after('customCode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('footer_configs', function (Blueprint $table) {
            if (Schema::hasColumn('footer_configs', 'bottom')) {
                $table->dropColumn('bottom');
            }
        });
    }
};

