<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question', 500);
                $table->longText('answer');
                $table->string('category', 100)->default('General');
                $table->integer('order')->default(0);
                $table->boolean('isPublished')->default(true);
                $table->timestamps();

                $table->index(['isPublished', 'order']);
                $table->index('category');
            });
        } else {
            Schema::table('faqs', function (Blueprint $table) {
                if (! Schema::hasColumn('faqs', 'isPublished')) {
                    $table->boolean('isPublished')->default(true)->after('order');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
