<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Portfolios (Module 5)
        if (Schema::hasTable('portfolios') && ! Schema::hasColumn('portfolios', 'imageAlt')) {
            Schema::table('portfolios', function (Blueprint $table) {
                $table->string('imageAlt', 500)->nullable()->after('image');
            });
        }

        // Blogs (Module 6)
        if (Schema::hasTable('blogs') && ! Schema::hasColumn('blogs', 'imageAlt')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->string('imageAlt', 500)->nullable()->after('image');
            });
        }

        // Events (Module 7)
        if (Schema::hasTable('events') && ! Schema::hasColumn('events', 'imageAlt')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('imageAlt', 500)->nullable()->after('image');
            });
        }

        // Team Members (Module 8)
        if (Schema::hasTable('team_members') && ! Schema::hasColumn('team_members', 'imageAlt')) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->string('imageAlt', 500)->nullable()->after('photo');
            });
        }

        // Testimonials (Module 9)
        if (Schema::hasTable('testimonials') && ! Schema::hasColumn('testimonials', 'imageAlt')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->string('imageAlt', 500)->nullable()->after('photo');
            });
        }

        // Galleries (Module 10)
        if (Schema::hasTable('galleries') && ! Schema::hasColumn('galleries', 'imageAlt')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->string('imageAlt', 500)->nullable()->after('coverImage');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('portfolios') && Schema::hasColumn('portfolios', 'imageAlt')) {
            Schema::table('portfolios', function (Blueprint $table) {
                $table->dropColumn('imageAlt');
            });
        }

        if (Schema::hasTable('blogs') && Schema::hasColumn('blogs', 'imageAlt')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropColumn('imageAlt');
            });
        }

        if (Schema::hasTable('events') && Schema::hasColumn('events', 'imageAlt')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('imageAlt');
            });
        }

        if (Schema::hasTable('team_members') && Schema::hasColumn('team_members', 'imageAlt')) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->dropColumn('imageAlt');
            });
        }

        if (Schema::hasTable('testimonials') && Schema::hasColumn('testimonials', 'imageAlt')) {
            Schema::table('testimonials', function (Blueprint $table) {
                $table->dropColumn('imageAlt');
            });
        }

        if (Schema::hasTable('galleries') && Schema::hasColumn('galleries', 'imageAlt')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->dropColumn('imageAlt');
            });
        }
    }
};

