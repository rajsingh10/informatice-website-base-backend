<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Next-Gen Web Architecture Summit',
                'slug' => 'next-gen-web-architecture-summit',
                'type' => 'Conference',
                'shortDescription' => 'Explore the cutting edge of web scalability, distributed systems, edge computing, and reactive frontend frameworks.',
                'longDescription' => '<p>Join industry pioneers and principal engineers as we dive deep into the future of scalable web systems. Over two packed days, attendees will learn how to design resilient micro-frontends, optimize CDN edge caching, and build high-throughput backends with modern async runtimes.</p><h3>What You Will Learn</h3><ul><li>High-concurrency distributed caching strategies</li><li>Edge SSR and streaming server-driven UI</li><li>Zero-downtime deployment topologies</li></ul>',
                'date' => '2027-06-15',
                'endDate' => '2027-06-16',
                'startTime' => '9:00 AM',
                'endTime' => '6:00 PM',
                'venue' => 'The Grand Convention Center, Delhi',
                'isOnline' => false,
                'onlineUrl' => '',
                'price' => 2999,
                'totalSeats' => 300,
                'availableSeats' => 42,
                'agenda' => [
                    [
                        'time' => '9:00 AM - 10:00 AM',
                        'topic' => 'Keynote: Next-Gen Web Architecture',
                    ],
                    [
                        'time' => '10:15 AM - 11:30 AM',
                        'topic' => 'Edge Runtimes and Global Caching Topologies',
                    ],
                    [
                        'time' => '1:00 PM - 2:30 PM',
                        'topic' => 'Scaling WebSocket Microservices to Millions of Connections',
                    ],
                    [
                        'time' => '3:00 PM - 5:00 PM',
                        'topic' => 'Live Architecture Teardown & Interactive Panel',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'Next-Gen Web Architecture Summit | Informatic Events',
                    'description' => 'Explore the cutting edge of web scalability, distributed systems, edge computing, and reactive frontend frameworks.',
                ],
            ],
            [
                'title' => 'AI & Machine Learning in Production Masterclass',
                'slug' => 'ai-ml-in-production-masterclass',
                'type' => 'Workshop',
                'shortDescription' => 'Practical, hands-on masterclass on fine-tuning LLMs, vector database search, and deploying low-latency AI agents.',
                'longDescription' => '<p>A deep-dive technical workshop designed for software engineers transitioning into AI engineering. Learn how to construct robust RAG pipelines, manage embeddings at scale, and implement guardrails for production applications.</p>',
                'date' => '2027-07-20',
                'endDate' => '2027-07-21',
                'startTime' => '10:00 AM',
                'endTime' => '4:00 PM',
                'venue' => 'Tech Hub Auditorium, Bengaluru',
                'isOnline' => false,
                'onlineUrl' => '',
                'price' => 1499,
                'totalSeats' => 120,
                'availableSeats' => 18,
                'agenda' => [
                    [
                        'time' => '10:00 AM - 12:00 PM',
                        'topic' => 'Vector Databases & Hybrid Search Architectures',
                    ],
                    [
                        'time' => '1:00 PM - 4:00 PM',
                        'topic' => 'Hands-on: Building Autonomous Agentic Workflows',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'AI & ML in Production Masterclass | Informatic Events',
                    'description' => 'Hands-on masterclass on fine-tuning LLMs, vector database search, and deploying low-latency AI agents.',
                ],
            ],
            [
                'title' => 'Global Cloud Native & DevOps Summit (Virtual)',
                'slug' => 'global-cloud-native-devops-summit',
                'type' => 'Webinar',
                'shortDescription' => 'Worldwide virtual gathering covering Kubernetes, GitOps workflows, automated canary deployments, and cost governance.',
                'longDescription' => '<p>Streamed globally to engineers in over 40 countries, this virtual summit covers state-of-the-art container orchestration, CI/CD pipelines, and cloud infrastructure reliability engineering.</p>',
                'date' => '2027-08-10',
                'endDate' => '2027-08-10',
                'startTime' => '3:00 PM',
                'endTime' => '7:00 PM',
                'venue' => 'Virtual Live Stream',
                'isOnline' => true,
                'onlineUrl' => 'https://zoom.us/j/9876543210',
                'price' => 0,
                'totalSeats' => 1000,
                'availableSeats' => 420,
                'agenda' => [
                    [
                        'time' => '3:00 PM - 4:00 PM',
                        'topic' => 'GitOps at Scale with ArgoCD and Kubernetes',
                    ],
                    [
                        'time' => '4:15 PM - 5:30 PM',
                        'topic' => 'Observability: OpenTelemetry, Prometheus & Distributed Traces',
                    ],
                    [
                        'time' => '5:45 PM - 7:00 PM',
                        'topic' => 'FinOps: Slashing Cloud Infrastructure Waste by 40%',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'Global Cloud Native & DevOps Summit | Informatic Events',
                    'description' => 'Worldwide virtual gathering covering Kubernetes, GitOps workflows, automated canary deployments, and cost governance.',
                ],
            ],
            [
                'title' => 'Modern Microservices with Go and Kubernetes',
                'slug' => 'modern-microservices-go-kubernetes',
                'type' => 'Workshop',
                'shortDescription' => 'Code-along intensive workshop focusing on building ultra-fast gRPC microservices with Go and managing them on k8s.',
                'longDescription' => '<p>Build microservices that benchmark at sub-millisecond response times. We cover protobuf definition, gRPC streaming, distributed transactions with the Saga pattern, and container security.</p>',
                'date' => '2027-09-05',
                'endDate' => '2027-09-06',
                'startTime' => '9:30 AM',
                'endTime' => '5:30 PM',
                'venue' => 'Innovation Center, Hyderabad',
                'isOnline' => false,
                'onlineUrl' => '',
                'price' => 3499,
                'totalSeats' => 80,
                'availableSeats' => 12,
                'agenda' => [
                    [
                        'time' => '9:30 AM - 12:30 PM',
                        'topic' => 'Writing Idiomatic, High-Performance Go Services',
                    ],
                    [
                        'time' => '1:30 PM - 5:30 PM',
                        'topic' => 'gRPC Interceptors, Deadlines, and Circuit Breaking',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'Modern Microservices with Go & Kubernetes | Informatic Events',
                    'description' => 'Code-along intensive workshop focusing on building ultra-fast gRPC microservices with Go.',
                ],
            ],
            [
                'title' => 'Enterprise Design Systems & UI Architecture',
                'slug' => 'enterprise-design-systems-ui-architecture',
                'type' => 'Seminar',
                'shortDescription' => 'Bridging the design-to-code gap in multi-brand enterprises through tokens, accessibility, and automated visual regression.',
                'longDescription' => '<p>Learn how industry-leading design teams structure Figma variables and design tokens that seamlessly sync to React, Vue, and mobile component libraries with automated CI visual regression testing.</p>',
                'date' => '2027-10-12',
                'endDate' => '2027-10-12',
                'startTime' => '2:00 PM',
                'endTime' => '6:00 PM',
                'venue' => 'Design Pavilion, Mumbai',
                'isOnline' => false,
                'onlineUrl' => '',
                'price' => 999,
                'totalSeats' => 150,
                'availableSeats' => 65,
                'agenda' => [
                    [
                        'time' => '2:00 PM - 3:30 PM',
                        'topic' => 'Design Token Architecture & Cross-Platform Sync',
                    ],
                    [
                        'time' => '4:00 PM - 6:00 PM',
                        'topic' => 'Accessibility (WCAG 2.2 AA) Built Into the Component Core',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'Enterprise Design Systems Seminar | Informatic Events',
                    'description' => 'Bridging the design-to-code gap in multi-brand enterprises through tokens and accessibility.',
                ],
            ],
            [
                'title' => 'Full-Stack Security & Zero Trust Architecture',
                'slug' => 'full-stack-security-zero-trust',
                'type' => 'Webinar',
                'shortDescription' => 'Practical mitigation strategies for OWASP Top 10 vulnerabilities, Passkey authentication, and secure OAuth2 implementation.',
                'longDescription' => '<p>Modern application security requires defense in depth: biometric WebAuthn / Passkeys, secure token exchange, automated dependency auditing, and cloud identity boundaries.</p>',
                'date' => '2027-11-18',
                'endDate' => '2027-11-18',
                'startTime' => '5:00 PM',
                'endTime' => '8:30 PM',
                'venue' => 'Online Webinar',
                'isOnline' => true,
                'onlineUrl' => 'https://zoom.us/j/1234567890',
                'price' => 499,
                'totalSeats' => 500,
                'availableSeats' => 195,
                'agenda' => [
                    [
                        'time' => '5:00 PM - 6:30 PM',
                        'topic' => 'Passkeys & Passwordless Authentication Architecture',
                    ],
                    [
                        'time' => '6:45 PM - 8:30 PM',
                        'topic' => 'OAuth2 & OIDC Security Best Practices in Single Page Apps',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'Full-Stack Security & Zero Trust | Informatic Events',
                    'description' => 'Practical mitigation strategies for OWASP Top 10 vulnerabilities, Passkey authentication, and secure OAuth2.',
                ],
            ],
            [
                'title' => 'Annual Informatic Developer Conference 2027',
                'slug' => 'annual-informatic-developer-conference-2027',
                'type' => 'Conference',
                'shortDescription' => 'Our flagship three-day tech celebration bringing together 1,200+ engineers, creators, founders, and digital architects.',
                'longDescription' => '<p>Three days, four tracks, 40+ speakers, and endless networking opportunities. Features live coding competitions, startup demo stages, and workshops led by engineering leaders from around the world.</p>',
                'date' => '2027-12-01',
                'endDate' => '2027-12-03',
                'startTime' => '9:00 AM',
                'endTime' => '6:30 PM',
                'venue' => 'Jio World Convention Centre, Mumbai',
                'isOnline' => false,
                'onlineUrl' => '',
                'price' => 4999,
                'totalSeats' => 1200,
                'availableSeats' => 350,
                'agenda' => [
                    [
                        'time' => 'Day 1: 9:00 AM - 6:00 PM',
                        'topic' => 'Keynotes, Platform Engineering & Architecture Tracks',
                    ],
                    [
                        'time' => 'Day 2: 9:00 AM - 6:00 PM',
                        'topic' => 'AI, Data Engineering, and High-Throughput Systems',
                    ],
                    [
                        'time' => 'Day 3: 9:00 AM - 4:00 PM',
                        'topic' => 'Hackathon Showcase, Award Ceremony & Closing Gala',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'Annual Informatic Developer Conference 2027',
                    'description' => 'Our flagship three-day tech celebration bringing together 1,200+ engineers, creators, and founders.',
                ],
            ],
            [
                'title' => 'Startup Scaleup & Product Strategy Forum',
                'slug' => 'startup-scaleup-product-strategy-forum',
                'type' => 'Meetup',
                'shortDescription' => 'Casual evening meetup for early-stage founders and product leads discussing product-market fit and customer retention.',
                'longDescription' => '<p>An intimate networking and knowledge-sharing evening. Hear from founders who scaled from zero to 100K active users without raising institutional venture capital.</p>',
                'date' => '2027-04-10',
                'endDate' => '2027-04-10',
                'startTime' => '11:00 AM',
                'endTime' => '3:00 PM',
                'venue' => 'CoWork Central, Pune',
                'isOnline' => false,
                'onlineUrl' => '',
                'price' => 0,
                'totalSeats' => 100,
                'availableSeats' => 25,
                'agenda' => [
                    [
                        'time' => '11:00 AM - 12:30 PM',
                        'topic' => 'Panel: Building What Customers Actually Pay For',
                    ],
                    [
                        'time' => '1:00 PM - 3:00 PM',
                        'topic' => 'Open Networking & Pitch Feedback Circles',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'Startup Scaleup & Product Strategy Forum | Informatic Events',
                    'description' => 'Casual meetup for early-stage founders discussing product-market fit and retention.',
                ],
            ],
            // Past event for testing ?when=past
            [
                'title' => 'Frontend Performance Summit 2025 (Archive)',
                'slug' => 'frontend-performance-summit-2025',
                'type' => 'Conference',
                'shortDescription' => 'Archive recordings and recap of our 2025 web performance conference.',
                'longDescription' => '<p>The complete archive of keynotes and technical sessions from our 2025 Frontend Performance Summit.</p>',
                'date' => '2025-01-15',
                'endDate' => '2025-01-16',
                'startTime' => '9:00 AM',
                'endTime' => '5:00 PM',
                'venue' => 'Virtual Archive',
                'isOnline' => true,
                'onlineUrl' => 'https://youtube.com/playlist?list=sample',
                'price' => 0,
                'totalSeats' => 500,
                'availableSeats' => 0,
                'agenda' => [
                    [
                        'time' => '9:00 AM - 11:00 AM',
                        'topic' => 'Core Web Vitals in 2025',
                    ],
                ],
                'image' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=1200',
                'status' => 'published',
                'seo' => [
                    'title' => 'Frontend Performance Summit 2025 Archive',
                    'description' => 'Archive recordings and recap of our 2025 web performance conference.',
                ],
            ],
        ];

        foreach ($events as $eventData) {
            Event::updateOrCreate(
                ['slug' => $eventData['slug']],
                $eventData
            );
        }

        $this->command->info(count($events).' events seeded successfully.');
    }
}
