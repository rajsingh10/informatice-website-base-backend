<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Career::count() > 0) {
            $this->command?->info('Careers already seeded.');

            return;
        }

        $careers = [
            [
                'title' => 'Senior Full Stack Engineer (Laravel & React)',
                'slug' => 'senior-full-stack-engineer-laravel-react',
                'department' => 'Engineering',
                'location' => 'Remote / San Francisco',
                'type' => 'full-time',
                'experience' => '4-6 years',
                'description' => '<p>We are seeking a seasoned Senior Full Stack Engineer to build high-scale, resilient enterprise web architectures and APIs using modern Laravel and React.</p><p>You will lead feature roadmaps, conduct code reviews, and optimize distributed database systems.</p>',
                'requirements' => [
                    '4+ years production experience with PHP/Laravel and modern React / TypeScript.',
                    'Deep understanding of RESTful API design, database indexing, and Redis caching.',
                    'Experience with Docker, CI/CD pipelines, and cloud deployment (AWS/GCP).',
                    'Demonstrated ability to write comprehensive automated feature tests.',
                ],
                'applyUrl' => 'mailto:careers@informatic.example.com?subject=Senior%20Full%20Stack%20Engineer',
                'status' => 'open',
                'order' => 1,
                'postedAt' => now()->subDays(5),
            ],
            [
                'title' => 'Lead Cloud DevOps & Infrastructure Architect',
                'slug' => 'lead-cloud-devops-infrastructure-architect',
                'department' => 'DevOps & Cloud',
                'location' => 'Remote / London',
                'type' => 'full-time',
                'experience' => '5+ years',
                'description' => '<p>Join our core infrastructure engineering group to lead multi-cloud provisioning, Kubernetes cluster orchestration, and automated zero-downtime deployment pipelines.</p>',
                'requirements' => [
                    'Proficiency in Terraform, AWS/GCP, Docker, and Kubernetes.',
                    'Experience designing SOC2 and ISO27001 compliant CI/CD workflows.',
                    'Solid understanding of observability with Prometheus, Grafana, and ELK stack.',
                ],
                'applyUrl' => 'mailto:careers@informatic.example.com?subject=Lead%20Cloud%20DevOps',
                'status' => 'open',
                'order' => 2,
                'postedAt' => now()->subDays(10),
            ],
            [
                'title' => 'Senior UI/UX Product Designer',
                'slug' => 'senior-ui-ux-product-designer',
                'department' => 'Design',
                'location' => 'Remote',
                'type' => 'full-time',
                'experience' => '3-5 years',
                'description' => '<p>We are looking for a Senior UI/UX Designer who can shape intuitive, modern user interfaces for SaaS platforms and enterprise web portals.</p>',
                'requirements' => [
                    'Mastery of Figma, design systems, and rapid prototyping.',
                    'Strong portfolio showcasing data-dense dashboard and enterprise UI work.',
                    'Deep empathy for user workflows and familiarity with design-to-code handoffs.',
                ],
                'applyUrl' => 'mailto:careers@informatic.example.com?subject=Senior%20UI%20UX%20Designer',
                'status' => 'open',
                'order' => 3,
                'postedAt' => now()->subDays(14),
            ],
            [
                'title' => 'AI & Machine Learning Solutions Engineer',
                'slug' => 'ai-machine-learning-solutions-engineer',
                'department' => 'AI & Data',
                'location' => 'Remote / New York',
                'type' => 'contract',
                'experience' => '3+ years',
                'description' => '<p>Help build generative AI integrations, LLM orchestration pipelines, and intelligent retrieval systems for client software.</p>',
                'requirements' => [
                    'Hands-on experience with Python, LangChain, vector databases, and embeddings.',
                    'Familiarity with fine-tuning, retrieval-augmented generation (RAG), and prompt engineering.',
                ],
                'applyUrl' => 'mailto:careers@informatic.example.com?subject=AI%20ML%20Engineer',
                'status' => 'open',
                'order' => 4,
                'postedAt' => now()->subDays(2),
            ],
            [
                'title' => 'Junior Frontend Developer (Internship)',
                'slug' => 'junior-frontend-developer-internship',
                'department' => 'Engineering',
                'location' => 'Remote',
                'type' => 'internship',
                'experience' => '0-1 year',
                'description' => '<p>Kickstart your software engineering career with our mentorship internship program. Build real components alongside senior engineers.</p>',
                'requirements' => [
                    'Strong fundamentals in HTML5, CSS3, JavaScript, and Tailwind CSS.',
                    'Eagerness to learn React and modern frontend workflows.',
                ],
                'applyUrl' => 'mailto:careers@informatic.example.com?subject=Frontend%20Internship',
                'status' => 'closed',
                'order' => 5,
                'postedAt' => now()->subMonths(1),
            ],
        ];

        foreach ($careers as $item) {
            Career::create($item);
        }

        $this->command?->info('Seeded '.count($careers).' career openings.');
    }
}
