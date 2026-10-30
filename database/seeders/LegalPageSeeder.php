<?php

namespace Database\Seeders;

use App\Models\LegalPage;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'type' => 'terms',
                'title' => 'Terms of Service',
                'content' => '<h2>1. Acceptance of Terms</h2><p>By accessing or utilizing the digital platform, engineering solutions, or cloud infrastructure provided by Informatic, you acknowledge that you have read, understood, and agreed to be legally bound by these Terms of Service.</p><h2>2. Intellectual Property Rights</h2><p>All software architecture, visual designs, trademarks, and documentation provided as baseline components remain the intellectual property of their respective owners. Deliverables contracted under bespoke agreements transfer in accordance with client contracts.</p><h2>3. Acceptable Use Policy</h2><p>Users shall not conduct security penetration tests without explicit prior written authorization, nor attempt unauthorized access, reverse-engineering, or denial of service attacks.</p><h2>4. Limitation of Liability</h2><p>In no event shall Informatic be liable for punitive, incidental, or consequential damages resulting from downtime or service interruptions outside scheduled maintenance periods.</p><h2>5. Governing Law</h2><p>These terms shall be governed by and construed in accordance with the laws of the jurisdiction specified in the master services agreement.</p>',
                'lastUpdated' => now()->format('F Y'),
            ],
            [
                'type' => 'privacy',
                'title' => 'Privacy Policy',
                'content' => '<h2>1. Information We Collect</h2><p>We collect corporate and contact information voluntarily submitted through inquiry forms, job applications, client onboarding workflows, and technical diagnostic telemetry.</p><h2>2. Purpose and Legal Basis for Processing</h2><p>Information collected is utilized strictly to provide enterprise engineering services, respond to project inquiries, evaluate career candidates, and maintain security monitoring across our infrastructure.</p><h2>3. Data Protection and Storage</h2><p>We enforce modern AES-256 encryption at rest, TLS 1.3 encryption in transit, strict role-based access restrictions, and regular independent security audits.</p><h2>4. Third-Party Sharing</h2><p>We do not monetize, rent, or sell client or candidate information. Data is shared exclusively with certified cloud and infrastructure partners required to deliver operational services under strict NDAs.</p><h2>5. Your Rights and Contact Information</h2><p>You may request access to, correction of, or deletion of your personal records at any time by contacting our Data Protection Officer at privacy@informatic.example.com.</p>',
                'lastUpdated' => now()->format('F Y'),
            ],
        ];

        foreach ($pages as $page) {
            LegalPage::updateOrCreate(
                ['type' => $page['type']],
                [
                    'title' => $page['title'],
                    'content' => $page['content'],
                    'lastUpdated' => $page['lastUpdated'],
                ]
            );
        }

        $this->command?->info('Seeded '.count($pages).' legal pages (terms & privacy).');
    }
}
