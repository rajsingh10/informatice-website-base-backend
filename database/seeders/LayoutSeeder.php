<?php

namespace Database\Seeders;

use App\Models\Layout;
use Illuminate\Database\Seeder;

class LayoutSeeder extends Seeder
{
    /**
     * Seed every layout key from the specification table with layout1.
     */
    public function run(): void
    {
        $keys = Layout::validKeys();

        foreach ($keys as $key) {
            Layout::updateOrCreate(
                ['key' => $key],
                [
                    'templateId' => 'layout1',
                    'updatedAt'  => now(),
                ]
            );
        }

        $this->command->info(count($keys) . ' layout keys seeded with layout1.');
    }
}
