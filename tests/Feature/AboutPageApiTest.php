<?php

namespace Tests\Feature;

use App\Models\AboutPage;
use App\Models\PageSeo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AboutPageApiTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('Admin@1234'),
                'role'     => 'super_admin',
            ]
        );
    }

    public function test_public_about_endpoint_returns_content_with_image_alt_attributes(): void
    {
        $response = $this->getJson('/api/public/about');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'badge',
                    'title',
                    'tagline',
                    'storyTitle',
                    'storyContent',
                    'heroImage',
                    'heroImageAlt',
                    'hero' => ['url', 'alt'],
                    'storyImage',
                    'storyImageAlt',
                    'story' => ['url', 'alt'],
                    'mission',
                    'vision',
                    'values',
                    'milestones',
                    'stats',
                    'images',
                    'imageAltMap',
                    'teamSectionEnabled',
                    'layout' => ['key', 'templateId', 'allowed'],
                    'seo' => ['title', 'description', 'robots', 'imageAltMap'],
                ],
            ]);

        // Assert direct ALT attributes are present and not empty
        $data = $response->json('data');
        $this->assertNotEmpty($data['heroImageAlt']);
        $this->assertNotEmpty($data['storyImageAlt']);
        $this->assertEquals($data['heroImageAlt'], $data['hero']['alt']);
        $this->assertEquals($data['storyImageAlt'], $data['story']['alt']);
    }

    public function test_direct_public_about_aliases_work(): void
    {
        $this->getJson('/about')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/public/about')->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_about_overview_and_feed(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $response = $this->getJson('/api/admin/about');
        $response->assertStatus(200)->assertJson(['success' => true]);

        $feedRes = $this->getJson('/api/admin/modules/about');
        $feedRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'module'  => 'about',
            ])
            ->assertJsonStructure([
                'success',
                'module',
                'data' => ['title', 'heroImageAlt', 'imageAltMap'],
            ]);
    }

    public function test_admin_update_about_content_and_image_alt_attributes(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $payload = [
            'badge'         => 'Who We Are',
            'title'         => 'Building Tomorrow Digital World Today',
            'tagline'       => 'Empowering modern enterprises with scalable AI and cloud architectures.',
            'storyTitle'    => 'Our 10-Year Journey',
            'storyContent'  => 'From a modest garage studio to an award-winning software engineering powerhouse.',
            'heroImage'     => 'https://images.unsplash.com/photo-custom-hero.jpg',
            'heroImageAlt'  => 'Diverse engineering team designing enterprise software architecture',
            'storyImage'    => 'https://images.unsplash.com/photo-custom-story.jpg',
            'storyImageAlt' => 'Software developers writing code at multi-monitor workstations',
            'mission'       => 'To empower our partners with bulletproof digital software solutions.',
            'vision'        => 'To lead global cloud innovation through reliable and ethical technology.',
            'values'        => [
                [
                    'title'       => 'Code Quality',
                    'description' => 'Zero-defect mentality and rigorous unit testing.',
                    'icon'        => 'fa-solid fa-gem',
                    'image'       => '',
                    'imageAlt'    => 'Quality engineering badges and code compliance seals',
                ],
            ],
            'images'        => [
                [
                    'slot' => 'img-about-culture',
                    'url'  => 'https://images.unsplash.com/photo-custom-culture.jpg',
                    'alt'  => 'Creative collaboration session in our open tech hub',
                ],
            ],
        ];

        $updateRes = $this->putJson('/api/admin/about', $payload);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'badge'        => 'Who We Are',
                    'title'        => 'Building Tomorrow Digital World Today',
                    'heroImageAlt' => 'Diverse engineering team designing enterprise software architecture',
                    'storyImageAlt'=> 'Software developers writing code at multi-monitor workstations',
                ],
            ]);

        // Verify updated values persisted
        $getRes = $this->getJson('/api/public/about');
        $data = $getRes->json('data');
        $this->assertEquals('Diverse engineering team designing enterprise software architecture', $data['heroImageAlt']);
        $this->assertEquals('Diverse engineering team designing enterprise software architecture', $data['hero']['alt']);
        $this->assertEquals('Diverse engineering team designing enterprise software architecture', $data['imageAltMap']['img-about-hero']);
    }

    public function test_admin_update_images_dedicated_endpoint(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $imagesPayload = [
            'heroImage'     => 'https://images.unsplash.com/hero-updated.jpg',
            'heroImageAlt'  => 'Updated hero image alt text for screen readers',
            'storyImage'    => 'https://images.unsplash.com/story-updated.jpg',
            'storyImageAlt' => 'Updated story image alt text for accessibility',
            'images'        => [
                [
                    'slot' => 'img-about-lab',
                    'url'  => 'https://images.unsplash.com/lab.jpg',
                    'alt'  => 'Cloud computing research laboratory with quantum servers',
                ],
            ],
        ];

        $res = $this->putJson('/api/admin/about/images', $imagesPayload);

        $res->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'heroImage'    => 'https://images.unsplash.com/hero-updated.jpg',
                    'heroImageAlt' => 'Updated hero image alt text for screen readers',
                    'storyImage'   => 'https://images.unsplash.com/story-updated.jpg',
                    'storyImageAlt'=> 'Updated story image alt text for accessibility',
                ],
            ]);

        $this->assertEquals(
            'Updated hero image alt text for screen readers',
            $res->json('data.imageAltMap.img-about-hero')
        );
    }

    public function test_seo_for_about_page_works_publicly_and_admin(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        // Public SEO read for 'about'
        $publicSeo = $this->getJson('/api/public/seo/about');
        $publicSeo->assertStatus(200)->assertJson(['success' => true]);

        // Admin SEO update for 'about'
        $updateSeo = $this->putJson('/api/admin/seo/about', [
            'title'       => 'About Us | NexGen Digital Innovation',
            'description' => 'Learn more about NexGen journey, engineering team, and our mission.',
            'keywords'    => 'about us, engineering team, cloud innovation, company story',
            'imageAltMap' => [
                'img-about-hero' => 'Executive leadership and engineering teams',
            ],
        ]);

        $updateSeo->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'title' => 'About Us | NexGen Digital Innovation',
                ],
            ]);

        // Verify public read reflects the updated SEO
        $verifySeo = $this->getJson('/api/public/seo/about');
        $verifySeo->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'About Us | NexGen Digital Innovation',
                ],
            ]);
    }

    public function test_admin_about_reset_endpoint(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $resetRes = $this->postJson('/api/admin/about/reset');
        $resetRes->assertStatus(200)->assertJson(['success' => true]);

        $about = AboutPage::getSingleton();
        $this->assertEquals('About Us', $about->badge);
        $this->assertNotEmpty($about->heroImageAlt);
    }

    public function test_admin_about_routes_require_authentication(): void
    {
        $this->putJson('/api/admin/about', ['title' => 'Unauthorized'])
            ->assertStatus(401);

        $this->putJson('/api/admin/about/images', ['heroImageAlt' => 'Unauthorized'])
            ->assertStatus(401);

        $this->postJson('/api/admin/about/reset')
            ->assertStatus(401);
    }
}

