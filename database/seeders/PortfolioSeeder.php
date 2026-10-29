<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        if (Portfolio::count() === 0) {
            Portfolio::create([
                'title'            => 'E-Commerce Platform',
                'slug'             => 'ecommerce-platform',
                'client'           => 'RetailMax',
                'year'             => '2024',
                'category'         => 'Web Development',
                'technologies'     => ['React', 'Node.js', 'MongoDB', 'Stripe'],
                'shortDescription' => 'High-converting omnichannel digital commerce experience for over 1M monthly shoppers.',
                'longDescription'  => '<p>We partnered with RetailMax to overhaul their legacy storefront into an ultrafast, headless commerce engine.</p>',
                'challenge'        => 'High shopping cart abandonment rates and slow page loads during peak holiday campaigns.',
                'solution'         => 'Replatformed to React Next.js with localized caching, serverless API microservices, and 1-click checkout.',
                'results'          => [
                    ['metric' => '+210%', 'label' => 'Online Revenue Increase'],
                    ['metric' => '0.8s',  'label' => 'Average Page Load Time'],
                    ['metric' => '42%',   'label' => 'Drop in Checkout Dropoffs'],
                ],
                'image'            => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
                'gallery'          => [
                    'https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80',
                ],
                'projectUrl'       => 'https://example.com/project/retailmax',
                'isFeatured'       => true,
                'order'            => 1,
                'isPublished'      => true,
                'seo'              => [
                    'title'       => 'RetailMax E-Commerce Transformation — Case Study',
                    'description' => 'How RetailMax boosted online revenues by 210% with a modern headless storefront.',
                ],
            ]);

            $this->command->info('Sample portfolio project seeded.');
        }
    }
}

