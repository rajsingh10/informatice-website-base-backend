<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'services' => 'order',
            'blogs' => 'status',
            'events' => 'status',
            'team_members' => 'isLeadership',
            'galleries' => 'order',
            'careers' => 'status',
            'faqs' => 'order',
        ];

        foreach ($tables as $tableName => $afterCol) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'isFeatured')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $afterCol) {
                    $col = $table->boolean('isFeatured')->default(false);
                    if (Schema::hasColumn($tableName, $afterCol)) {
                        $col->after($afterCol);
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = ['services', 'blogs', 'events', 'team_members', 'galleries', 'careers', 'faqs'];
        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'isFeatured')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('isFeatured');
                });
            }
        }
    }
};

