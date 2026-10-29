<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Testimonial::count() > 0) {
            $this->command?->info('Testimonials already seeded.');

            return;
        }

        $testimonials = [
            [
                'name' => 'Rahul Sharma',
                'designation' => 'CEO',
                'company' => 'TechVista Solutions',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&auto=format&fit=crop&q=80',
                'rating' => 5,
                'text' => 'Informatic transformed our digital presence completely. Their engineering architecture sped up our platform load time by 3x and helped us scale to 500,000 active users seamlessly.',
                'isFeatured' => true,
                'videoUrl' => '',
                'metric' => [
                    'label' => 'Conversion Lift',
                    'value' => '+240%',
                ],
                'order' => 1,
                'isPublished' => true,
            ],
            [
                'name' => 'Elena Rostova',
                'designation' => 'VP of Engineering',
                'company' => 'CloudScale Global',
                'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&auto=format&fit=crop&q=80',
                'rating' => 5,
                'text' => 'The microservices architecture and zero-downtime deployment pipelines designed by their team allowed us to deploy multiple times a day without a single minute of downtime.',
                'isFeatured' => true,
                'videoUrl' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'metric' => [
                    'label' => 'Latency Reduction',
                    'value' => '-65%',
                ],
                'order' => 2,
                'isPublished' => true,
            ],
            [
                'name' => 'Marcus Vance',
                'designation' => 'Managing Director',
                'company' => 'FinTech Ventures',
                'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=800&auto=format&fit=crop&q=80',
                'rating' => 5,
                'text' => 'Security, compliance, and blazing performance were paramount for our payment gateway. Informatic delivered a robust, audited system ahead of schedule.',
                'isFeatured' => true,
                'videoUrl' => '',
                'metric' => [
                    'label' => 'Time to Market',
                    'value' => '3x Faster',
                ],
                'order' => 3,
                'isPublished' => true,
            ],
            [
                'name' => 'Aarti Singhania',
                'designation' => 'Head of Product',
                'company' => 'RetailHub India',
                'photo' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=800&auto=format&fit=crop&q=80',
                'rating' => 4,
                'text' => 'Outstanding UX design and mobile-responsive checkout flow. Our mobile cart abandonment decreased significantly within the first month after launch.',
                'isFeatured' => false,
                'videoUrl' => '',
                'metric' => [
                    'label' => 'Mobile GMV',
                    'value' => '+180%',
                ],
                'order' => 4,
                'isPublished' => true,
            ],
            [
                'name' => 'David Chen',
                'designation' => 'Founder & CTO',
                'company' => 'DataPulse Labs',
                'photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=800&auto=format&fit=crop&q=80',
                'rating' => 5,
                'text' => 'Their deep expertise in distributed data indexing and AI pipeline orchestration made our enterprise analytics software blazing fast and cost-effective.',
                'isFeatured' => false,
                'videoUrl' => '',
                'metric' => [
                    'label' => 'Cloud Cost Savings',
                    'value' => '$450K/yr',
                ],
                'order' => 5,
                'isPublished' => true,
            ],
            [
                'name' => 'Sarah Jenkins',
                'designation' => 'Director of Digital Transformation',
                'company' => 'Apex Logistics',
                'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80',
                'rating' => 5,
                'text' => 'Working with Informatic felt like having an elite Silicon Valley engineering squad embedded right inside our company. Truly phenomenal work.',
                'isFeatured' => true,
                'videoUrl' => '',
                'metric' => [
                    'label' => 'Workflow Automation',
                    'value' => '99.9%',
                ],
                'order' => 6,
                'isPublished' => true,
            ],
        ];

        foreach ($testimonials as $data) {
            Testimonial::create($data);
        }

        $this->command?->info('Seeded '.count($testimonials).' testimonials.');
    }
}
