<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Testimonial;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TestimonialApiTest extends TestCase
{
    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('Admin@1234'),
                'is_admin' => true,
            ]
        );
    }

    public function test_public_testimonials_feed_returns_published_reviews(): void
    {
        $response = $this->getJson('/api/public/testimonials');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'designation',
                        'company',
                        'photo',
                        'rating',
                        'text',
                        'isFeatured',
                        'videoUrl',
                        'metric' => [
                            'label',
                            'value',
                        ],
                        'order',
                        'isPublished',
                    ],
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertIsArray($response->json('data'));
    }

    public function test_public_testimonials_supports_featured_and_rating_filters(): void
    {
        $featuredResponse = $this->getJson('/api/public/testimonials?featured=1');
        $featuredResponse->assertStatus(200);

        foreach ($featuredResponse->json('data') as $review) {
            $this->assertTrue($review['isFeatured']);
        }

        $ratingResponse = $this->getJson('/api/public/testimonials?rating=5');
        $ratingResponse->assertStatus(200);

        foreach ($ratingResponse->json('data') as $review) {
            $this->assertEquals(5, $review['rating']);
        }
    }

    public function test_public_single_testimonial_by_id(): void
    {
        $review = Testimonial::published()->first();
        $this->assertNotNull($review, 'Seeded testimonial expected for test.');

        $response = $this->getJson("/api/public/testimonials/{$review->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $review->id,
                    'name' => $review->name,
                    'text' => $review->text,
                ],
            ]);
    }

    public function test_public_testimonial_returns_404_for_unknown(): void
    {
        $response = $this->getJson('/api/public/testimonials/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Testimonial not found.',
            ]);
    }

    public function test_admin_testimonials_endpoints_require_authentication(): void
    {
        $this->getJson('/api/admin/testimonials')->assertStatus(401);
        $this->getJson('/api/admin/testimonials/1')->assertStatus(401);
        $this->postJson('/api/admin/testimonials', [])->assertStatus(401);
        $this->putJson('/api/admin/testimonials/1', [])->assertStatus(401);
        $this->deleteJson('/api/admin/testimonials/1')->assertStatus(401);
        $this->getJson('/api/admin/modules/testimonials')->assertStatus(401);
    }

    public function test_admin_can_access_module_overview(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->getJson('/api/admin/modules/testimonials');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'module',
                'data' => [
                    'layout' => [
                        'key',
                        'templateId',
                        'allowed',
                    ],
                    'total',
                    'testimonials',
                ],
            ]);

        $this->assertEquals('testimonials', $response->json('module'));
        $this->assertEquals('testimonialsPageLayout', $response->json('data.layout.key'));
    }

    public function test_admin_can_crud_testimonials(): void
    {
        Passport::actingAs($this->admin);

        // 1. Create
        $createPayload = [
            'name' => 'Jennifer Miller',
            'designation' => 'Chief Operations Officer',
            'company' => 'Logistics Velocity',
            'photo' => 'https://example.com/client.jpg',
            'rating' => 5,
            'text' => 'Their automated architecture cut our dispatch latency by half.',
            'isFeatured' => true,
            'videoUrl' => 'https://example.com/video.mp4',
            'metric' => [
                'label' => 'Dispatch Speed',
                'value' => '2x Faster',
            ],
            'order' => 50,
            'isPublished' => true,
        ];

        $createResponse = $this->postJson('/api/admin/testimonials', $createPayload);
        $createResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Jennifer Miller',
                    'designation' => 'Chief Operations Officer',
                    'company' => 'Logistics Velocity',
                    'rating' => 5,
                    'isFeatured' => true,
                ],
            ]);

        $id = $createResponse->json('data.id');
        $this->assertNotNull($id);

        // 2. Read Single
        $showResponse = $this->getJson("/api/admin/testimonials/{$id}");
        $showResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'name' => 'Jennifer Miller',
                ],
            ]);

        // 3. Update
        $updateResponse = $this->putJson("/api/admin/testimonials/{$id}", [
            'rating' => 4,
            'isFeatured' => false,
        ]);
        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'rating' => 4,
                    'isFeatured' => false,
                ],
            ]);

        // 4. Delete
        $deleteResponse = $this->deleteJson("/api/admin/testimonials/{$id}");
        $deleteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Testimonial deleted successfully.',
            ]);

        // Verify Deletion
        $this->getJson("/api/admin/testimonials/{$id}")->assertStatus(404);
    }

    public function test_testimonial_validates_required_fields(): void
    {
        Passport::actingAs($this->admin);

        // Missing all required fields
        $response = $this->postJson('/api/admin/testimonials', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'name',
                    'text',
                ],
            ]);

        // Invalid rating (out of range 1-5)
        $invalidRating = $this->postJson('/api/admin/testimonials', [
            'name' => 'Test User',
            'text' => 'Great work!',
            'rating' => 10,
        ]);

        $invalidRating->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }
}
