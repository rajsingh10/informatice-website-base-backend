<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Gallery::count() > 0) {
            $this->command?->info('Gallery albums already seeded.');

            return;
        }

        $albums = [
            [
                'name' => 'Office & Culture',
                'category' => 'Culture',
                'description' => 'A glimpse into our collaborative workspaces, open innovation labs, and team life.',
                'coverImage' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&auto=format&fit=crop&q=80',
                'images' => [
                    [
                        'id' => 1,
                        'url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'Innovation Hub Floor',
                        'caption' => 'Open floor plan fostering cross-disciplinary collaboration.',
                    ],
                    [
                        'id' => 2,
                        'url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'Pair Programming Lounge',
                        'caption' => 'Engineers reviewing architecture blueprints together.',
                    ],
                    [
                        'id' => 3,
                        'url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'Town Hall Presentation',
                        'caption' => 'Quarterly all-hands tech retrospective and product showcase.',
                    ],
                    [
                        'id' => 4,
                        'url' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'Design Review Suite',
                        'caption' => 'Critiquing new interface prototypes with clients.',
                    ],
                ],
                'order' => 1,
                'isPublished' => true,
            ],
            [
                'name' => 'Tech & Hackathons',
                'category' => 'Events',
                'description' => 'Snapshots from our annual builder summit, 48-hour buildathons, and keynote presentations.',
                'coverImage' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&auto=format&fit=crop&q=80',
                'images' => [
                    [
                        'id' => 1,
                        'url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'Keynote Main Stage',
                        'caption' => 'Opening ceremony of the Developer Summit 2026.',
                    ],
                    [
                        'id' => 2,
                        'url' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'Hackathon Build Room',
                        'caption' => 'Teams competing in autonomous LLM agent development.',
                    ],
                    [
                        'id' => 3,
                        'url' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'Auditorium Panel Discussion',
                        'caption' => 'Industry veterans answering crowd questions about edge runtimes.',
                    ],
                ],
                'order' => 2,
                'isPublished' => true,
            ],
            [
                'name' => 'Product & Design Sprints',
                'category' => 'Workplace',
                'description' => 'Brainstorming workshops, user journey mapping, and rapid iterative design sprints.',
                'coverImage' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=1200&auto=format&fit=crop&q=80',
                'images' => [
                    [
                        'id' => 1,
                        'url' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'User Journey Wall',
                        'caption' => 'Mapping enterprise user workflows on interactive boards.',
                    ],
                    [
                        'id' => 2,
                        'url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1200&auto=format&fit=crop&q=80',
                        'title' => 'Rapid Prototyping Workshop',
                        'caption' => 'Collaborating on design token systems and layout components.',
                    ],
                ],
                'order' => 3,
                'isPublished' => true,
            ],
        ];

        foreach ($albums as $data) {
            Gallery::create($data);
        }

        $this->command?->info('Seeded '.count($albums).' gallery albums.');
    }
}
