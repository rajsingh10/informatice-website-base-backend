<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::count() === 0) {
            Service::create([
                'title'            => 'Web Development',
                'slug'             => 'web-development',
                'category'         => 'Development',
                'shortDescription' => 'Custom websites built with modern technologies.',
                'fullDescription'  => '<p>We craft high-performance, robust, and scalable web applications customized to your enterprise needs.</p>',
                'icon'             => 'Globe',
                'image'            => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80',
                'features'         => ['Responsive Design', 'SEO Optimized', 'Blazing Fast Performance'],
                'deliverables'     => ['Custom UI/UX Architecture', 'Full API Backend Integration', 'CI/CD Automated Deployment'],
                'order'            => 1,
                'isPublished'      => true,
                'seo'              => [
                    'title'       => 'Web Development Services — NexGen Solutions',
                    'description' => 'Scalable enterprise web applications designed for conversion and speed.',
                    'keywords'    => 'web development, custom react apps, laravel api',
                ],
            ]);

            Service::create([
                'title'            => 'Mobile App Development',
                'slug'             => 'mobile-app-development',
                'category'         => 'Mobile',
                'shortDescription' => 'Native and cross-platform mobile apps for iOS and Android.',
                'fullDescription'  => '<p>Bring your product to your customers hands with intuitive mobile user experiences.</p>',
                'icon'             => 'Smartphone',
                'image'            => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=1200&q=80',
                'features'         => ['iOS & Android', 'Offline Mode', 'Push Notifications'],
                'deliverables'     => ['Native App Package', 'App Store Publishing', 'Post-Launch Support'],
                'order'            => 2,
                'isPublished'      => true,
            ]);

            $this->command->info('Sample services seeded.');
        }
    }
}

