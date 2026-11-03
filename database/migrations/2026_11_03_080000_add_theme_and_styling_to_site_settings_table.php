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
        if (Schema::hasTable('site_settings')) {
            Schema::table('site_settings', function (Blueprint $table) {
                // Grouped JSON columns
                if (! Schema::hasColumn('site_settings', 'spacing')) {
                    $table->json('spacing')->nullable();
                }
                if (! Schema::hasColumn('site_settings', 'textDesign')) {
                    $table->json('textDesign')->nullable();
                }
                if (! Schema::hasColumn('site_settings', 'buttonStyle')) {
                    $table->json('buttonStyle')->nullable();
                }
                if (! Schema::hasColumn('site_settings', 'advanced')) {
                    $table->json('advanced')->nullable();
                }
                if (! Schema::hasColumn('site_settings', 'branding')) {
                    $table->json('branding')->nullable();
                }

                // Individual Spacing columns
                if (! Schema::hasColumn('site_settings', 'outerMargin')) {
                    $table->string('outerMargin', 50)->nullable()->default('0px');
                }
                if (! Schema::hasColumn('site_settings', 'paddingX')) {
                    $table->string('paddingX', 50)->nullable()->default('16px');
                }
                if (! Schema::hasColumn('site_settings', 'paddingY')) {
                    $table->string('paddingY', 50)->nullable()->default('24px');
                }
                if (! Schema::hasColumn('site_settings', 'borderRadius')) {
                    $table->string('borderRadius', 50)->nullable()->default('12px');
                }
                if (! Schema::hasColumn('site_settings', 'cardRadius')) {
                    $table->string('cardRadius', 50)->nullable()->default('16px');
                }

                // Individual Text design columns
                if (! Schema::hasColumn('site_settings', 'fontFamilyHeading')) {
                    $table->string('fontFamilyHeading', 100)->nullable()->default('Plus Jakarta Sans');
                }
                if (! Schema::hasColumn('site_settings', 'fontFamilyBody')) {
                    $table->string('fontFamilyBody', 100)->nullable()->default('Inter');
                }
                if (! Schema::hasColumn('site_settings', 'fontSizeBase')) {
                    $table->string('fontSizeBase', 50)->nullable()->default('16px');
                }
                if (! Schema::hasColumn('site_settings', 'fontWeightHeading')) {
                    $table->string('fontWeightHeading', 50)->nullable()->default('700');
                }
                if (! Schema::hasColumn('site_settings', 'fontWeightBody')) {
                    $table->string('fontWeightBody', 50)->nullable()->default('400');
                }

                // Individual Button style columns
                if (! Schema::hasColumn('site_settings', 'buttonRadius')) {
                    $table->string('buttonRadius', 50)->nullable()->default('8px');
                }
                if (! Schema::hasColumn('site_settings', 'buttonPaddingX')) {
                    $table->string('buttonPaddingX', 50)->nullable()->default('20px');
                }
                if (! Schema::hasColumn('site_settings', 'buttonPaddingY')) {
                    $table->string('buttonPaddingY', 50)->nullable()->default('10px');
                }
                if (! Schema::hasColumn('site_settings', 'buttonShadow')) {
                    $table->string('buttonShadow', 50)->nullable()->default('sm');
                }
                if (! Schema::hasColumn('site_settings', 'buttonHoverAnimation')) {
                    $table->string('buttonHoverAnimation', 50)->nullable()->default('scale');
                }

                // Individual Advanced columns
                if (! Schema::hasColumn('site_settings', 'customHeaderCode')) {
                    $table->longText('customHeaderCode')->nullable();
                }
                if (! Schema::hasColumn('site_settings', 'customFooterCode')) {
                    $table->longText('customFooterCode')->nullable();
                }
                if (! Schema::hasColumn('site_settings', 'googleAnalyticsId')) {
                    $table->string('googleAnalyticsId', 100)->nullable();
                }

                // Individual Branding columns
                if (! Schema::hasColumn('site_settings', 'primaryColor')) {
                    $table->string('primaryColor', 50)->nullable()->default('#2563eb');
                }
                if (! Schema::hasColumn('site_settings', 'secondaryColor')) {
                    $table->string('secondaryColor', 50)->nullable()->default('#475569');
                }
                if (! Schema::hasColumn('site_settings', 'accentColor')) {
                    $table->string('accentColor', 50)->nullable()->default('#f59e0b');
                }
                if (! Schema::hasColumn('site_settings', 'textColor')) {
                    $table->string('textColor', 50)->nullable()->default('#0f172a');
                }
                if (! Schema::hasColumn('site_settings', 'backgroundColor')) {
                    $table->string('backgroundColor', 50)->nullable()->default('#ffffff');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('site_settings')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $columns = [
                    'spacing', 'textDesign', 'buttonStyle', 'advanced', 'branding',
                    'outerMargin', 'paddingX', 'paddingY', 'borderRadius', 'cardRadius',
                    'fontFamilyHeading', 'fontFamilyBody', 'fontSizeBase', 'fontWeightHeading', 'fontWeightBody',
                    'buttonRadius', 'buttonPaddingX', 'buttonPaddingY', 'buttonShadow', 'buttonHoverAnimation',
                    'customHeaderCode', 'customFooterCode', 'googleAnalyticsId',
                    'primaryColor', 'secondaryColor', 'accentColor', 'textColor', 'backgroundColor',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('site_settings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

