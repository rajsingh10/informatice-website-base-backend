<?php

namespace Database\Seeders;

use App\Models\Layout;
use Illuminate\Database\Seeder;

class LayoutSeeder extends Seeder
{
    /**
     * Seed every layout key with layout1 (default).
     * Uses updateOrCreate so re-running is safe.
     */
    public function run(): void
    {
        $defaults = [
            'topbarLayout'   => 'layout1',
            'navbarLayout'   => 'layout1',
            'homePageLayout' => 'layout1',
            'footerLayout'   => 'layout1',
        ];

        foreach ($defaults as $key => $templateId) {
            Layout::updateOrCreate(
                ['key' => $key],
                ['templateId' => $templateId, 'updatedAt' => now()]
            );
        }

        $this->command->info('Layout defaults seeded (all keys → layout1).');
    }
}
