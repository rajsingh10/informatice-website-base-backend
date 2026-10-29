<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title'       => 'The Evolution of Design Systems in Enterprise Software',
                'slug'        => 'evolution-of-design-systems',
                'excerpt'     => 'How modern enterprise teams bridge the gap between design and engineering with unified token-based design systems.',
                'content'     => '<h2>Why Design Systems Matter</h2><p>As organizations grow, UI consistency becomes a critical challenge. Design tokens, reusable components, and strict style guidelines ensure that every application in the ecosystem presents a cohesive user experience.</p><h3>The Power of Tokens</h3><p>Tokens store design choices such as colors, typography, spacing, and elevation in platform-agnostic key-value pairs.</p>',
                'image'       => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200',
                'category'    => 'Design',
                'author'      => [
                    'name'        => 'Vikram Singh',
                    'designation' => 'Founder & CEO',
                    'avatar'      => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200',
                ],
                'date'        => '2025-03-15',
                'readTime'    => '5 min',
                'tags'        => ['UI/UX', 'Trends', 'Design Systems'],
                'status'      => 'published',
                'seo'         => [
                    'title'       => 'The Evolution of Design Systems in Enterprise Software',
                    'description' => 'How modern enterprise teams bridge the gap between design and engineering with unified design systems.',
                ],
            ],
            [
                'title'       => 'Architecting High-Throughput APIs with Node.js and Redis',
                'slug'        => 'high-throughput-apis-nodejs-redis',
                'excerpt'     => 'Proven strategies for scaling web APIs to 50,000+ requests per second using intelligent caching and event queues.',
                'content'     => '<h2>Scaling to the Millions</h2><p>High traffic workloads require optimization at every level: network, caching, database indexing, and asynchronous queue workers.</p><h3>Redis as a Tier-1 Cache</h3><p>By offloading read queries to Redis in-memory clusters, backend response times drop from hundreds of milliseconds to sub-10ms.</p>',
                'image'       => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=1200',
                'category'    => 'Engineering',
                'author'      => [
                    'name'        => 'Ananya Sharma',
                    'designation' => 'Chief Technology Officer',
                    'avatar'      => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200',
                ],
                'date'        => '2025-03-10',
                'readTime'    => '4 min',
                'tags'        => ['Backend', 'APIs', 'Redis', 'Performance'],
                'status'      => 'published',
                'seo'         => [],
            ],
            [
                'title'       => 'AI in FinTech: Fraud Detection & Real-time Risk Assessment',
                'slug'        => 'ai-in-fintech-fraud-detection',
                'excerpt'     => 'How machine learning algorithms process financial transactions in real time to prevent fraud and reduce false positives.',
                'content'     => '<h2>The New Frontier of Financial Security</h2><p>Legacy rule-based fraud detection fails against sophisticated attacks. Machine learning models trained on behavioral anomalies provide unprecedented fraud prevention accuracy.</p>',
                'image'       => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=1200',
                'category'    => 'Artificial Intelligence',
                'author'      => [
                    'name'        => 'Rohan Mehta',
                    'designation' => 'Lead Data Scientist',
                    'avatar'      => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200',
                ],
                'date'        => '2025-02-28',
                'readTime'    => '6 min',
                'tags'        => ['AI', 'FinTech', 'Machine Learning'],
                'status'      => 'published',
                'seo'         => [],
            ],
            [
                'title'       => 'Upcoming: Next-Gen Cloud Security Protocols (Scheduled Draft)',
                'slug'        => 'upcoming-cloud-security-protocols',
                'excerpt'     => 'A forward-looking exploration into zero-trust network architectures for global remote teams.',
                'content'     => '<h2>Zero Trust Fundamentals</h2><p>Never trust, always verify. This upcoming guide covers perimeter-less cloud deployments.</p>',
                'image'       => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200',
                'category'    => 'Security',
                'author'      => [
                    'name'        => 'Vikram Singh',
                    'designation' => 'Founder & CEO',
                    'avatar'      => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200',
                ],
                'date'        => '2026-12-01',
                'readTime'    => '3 min',
                'tags'        => ['Security', 'Cloud', 'Zero-Trust'],
                'status'      => 'scheduled',
                'seo'         => [],
            ],
        ];

        foreach ($posts as $postData) {
            Blog::updateOrCreate(
                ['slug' => $postData['slug']],
                $postData
            );
        }
    }
}

