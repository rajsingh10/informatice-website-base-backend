<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (TeamMember::count() > 0) {
            $this->command?->info('Team members already seeded.');

            return;
        }

        $members = [
            [
                'name' => 'Vikram Singh',
                'designation' => 'Founder & CEO',
                'department' => 'Management',
                'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&auto=format&fit=crop&q=80',
                'bio' => '15+ years of experience in tech leadership, enterprise architecture, and scaling digital products globally.',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/vikramsingh',
                    'twitter' => 'https://x.com/vikramsingh',
                    'github' => 'https://github.com/vikramsingh',
                ],
                'isLeadership' => true,
                'order' => 1,
                'isPublished' => true,
            ],
            [
                'name' => 'Priya Sharma',
                'designation' => 'Chief Technology Officer',
                'department' => 'Management',
                'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&auto=format&fit=crop&q=80',
                'bio' => 'Distributed systems specialist and ex-FAANG architect guiding engineering strategy and cloud-native standards.',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/priyasharma',
                    'twitter' => 'https://x.com/priyasharma',
                    'github' => 'https://github.com/priyasharma',
                ],
                'isLeadership' => true,
                'order' => 2,
                'isPublished' => true,
            ],
            [
                'name' => 'Aarav Mehta',
                'designation' => 'VP of Product Design',
                'department' => 'Design',
                'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=800&auto=format&fit=crop&q=80',
                'bio' => 'Design system evangelist passionate about micro-interactions, accessibility compliance, and user psychology.',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/aaravmehta',
                    'twitter' => 'https://x.com/aaravmehta',
                    'github' => 'https://github.com/aaravmehta',
                ],
                'isLeadership' => true,
                'order' => 3,
                'isPublished' => true,
            ],
            [
                'name' => 'Ananya Roy',
                'designation' => 'Principal Cloud Architect',
                'department' => 'Engineering',
                'photo' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=800&auto=format&fit=crop&q=80',
                'bio' => 'Certified Kubernetes Administrator and AWS Solutions Architect with a deep focus on high-availability clusters.',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/ananyaroy',
                    'twitter' => 'https://x.com/ananyaroy',
                    'github' => 'https://github.com/ananyaroy',
                ],
                'isLeadership' => false,
                'order' => 4,
                'isPublished' => true,
            ],
            [
                'name' => 'Rohan Verma',
                'designation' => 'Lead Full-Stack Engineer',
                'department' => 'Engineering',
                'photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=800&auto=format&fit=crop&q=80',
                'bio' => 'TypeScript & Go purist specializing in reactive frontend architecture, state engines, and ultra-fast gRPC services.',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/rohanverma',
                    'twitter' => 'https://x.com/rohanverma',
                    'github' => 'https://github.com/rohanverma',
                ],
                'isLeadership' => false,
                'order' => 5,
                'isPublished' => true,
            ],
            [
                'name' => 'Sneha Patel',
                'designation' => 'Head of Growth & Marketing',
                'department' => 'Marketing',
                'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80',
                'bio' => 'Data-driven marketing strategist driving global brand authority, customer acquisition, and partner ecosystems.',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/snehapatel',
                    'twitter' => 'https://x.com/snehapatel',
                    'github' => '',
                ],
                'isLeadership' => true,
                'order' => 6,
                'isPublished' => true,
            ],
            [
                'name' => 'Devendra Kulkarni',
                'designation' => 'Senior Site Reliability Engineer',
                'department' => 'Engineering',
                'photo' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=800&auto=format&fit=crop&q=80',
                'bio' => 'Specializes in zero-downtime blue/green infrastructure, automated synthetic testing, and observability.',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/devendrakulkarni',
                    'twitter' => 'https://x.com/devendrak',
                    'github' => 'https://github.com/devendrak',
                ],
                'isLeadership' => false,
                'order' => 7,
                'isPublished' => true,
            ],
            [
                'name' => 'Meera Nair',
                'designation' => 'Senior AI/ML Research Engineer',
                'department' => 'Engineering',
                'photo' => 'https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?w=800&auto=format&fit=crop&q=80',
                'bio' => 'Researches contextual embedding retrieval, fine-tuning small parameter models, and low-latency inference pipelines.',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/meeranair',
                    'twitter' => 'https://x.com/meeranair',
                    'github' => 'https://github.com/meeranair',
                ],
                'isLeadership' => false,
                'order' => 8,
                'isPublished' => true,
            ],
        ];

        foreach ($members as $data) {
            TeamMember::create($data);
        }

        $this->command?->info('Seeded '.count($members).' team members.');
    }
}
