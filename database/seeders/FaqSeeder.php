<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Faq::count() > 0) {
            $this->command?->info('FAQs already seeded.');

            return;
        }

        $faqs = [
            [
                'question' => 'What software engineering services does Informatic provide?',
                'answer' => '<p>We provide full-cycle engineering services including enterprise web & mobile application development, cloud architecture and DevOps, generative AI integrations, system modernization, and dedicated engineering teams.</p>',
                'category' => 'General',
                'order' => 1,
                'isPublished' => true,
            ],
            [
                'question' => 'How does Informatic guarantee project delivery timelines and code quality?',
                'answer' => '<p>We operate on Agile sprints with bi-weekly milestone demos, automated continuous integration (CI) test suites, rigorous peer code reviews, and transparent sprint tracking through Jira and GitHub.</p>',
                'category' => 'Process',
                'order' => 2,
                'isPublished' => true,
            ],
            [
                'question' => 'What engagement and pricing models are available?',
                'answer' => '<p>We offer flexible models tailored to your requirements: Dedicated Engineering Squads (monthly retainers), Time & Materials for evolving roadmaps, and Fixed-Price Milestone contracts for well-defined scopes.</p>',
                'category' => 'Billing & Contracts',
                'order' => 3,
                'isPublished' => true,
            ],
            [
                'question' => 'Who owns the intellectual property (IP) and source code?',
                'answer' => '<p>You maintain 100% ownership of all source code, architecture blueprints, data models, and proprietary assets created during the project. Full IP transfer occurs upon delivery.</p>',
                'category' => 'Billing & Contracts',
                'order' => 4,
                'isPublished' => true,
            ],
            [
                'question' => 'How do you handle security and data privacy?',
                'answer' => '<p>All our systems adhere to enterprise-grade security standards including OWASP Top 10 guidelines, encrypted data-at-rest and in-transit (AES-256 and TLS 1.3), SOC2 Type II compliance, and strict role-based access control.</p>',
                'category' => 'Security',
                'order' => 5,
                'isPublished' => true,
            ],
            [
                'question' => 'Do you provide post-launch maintenance and 24/7 SLA support?',
                'answer' => '<p>Yes, we provide tiered post-launch support and managed cloud operations with guaranteed 99.9% uptime SLAs, automated health monitoring, security patches, and rapid incident response.</p>',
                'category' => 'Support',
                'order' => 6,
                'isPublished' => true,
            ],
        ];

        foreach ($faqs as $item) {
            Faq::create($item);
        }

        $this->command?->info('Seeded '.count($faqs).' FAQ items.');
    }
}
