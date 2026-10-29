<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Gallery;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GalleryApiTest extends TestCase
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

    public function test_public_gallery_feed_returns_published_albums_with_images(): void
    {
        $response = $this->getJson('/api/public/gallery');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'category',
                        'description',
                        'coverImage',
                        'images' => [
                            '*' => [
                                'id',
                                'url',
                                'title',
                                'caption',
                            ],
                        ],
                        'order',
                        'isPublished',
                    ],
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertIsArray($response->json('data'));
    }

    public function test_public_gallery_feed_supports_category_filter(): void
    {
        $response = $this->getJson('/api/public/gallery?category=Events');
        $response->assertStatus(200);

        foreach ($response->json('data') as $album) {
            $this->assertEquals('Events', $album['category']);
        }
    }

    public function test_public_single_gallery_by_id(): void
    {
        $album = Gallery::published()->first();
        $this->assertNotNull($album, 'Seeded gallery album expected for test.');

        $response = $this->getJson("/api/public/gallery/{$album->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $album->id,
                    'name' => $album->name,
                ],
            ]);
    }

    public function test_public_gallery_returns_404_for_unknown(): void
    {
        $response = $this->getJson('/api/public/gallery/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Gallery album not found.',
            ]);
    }

    public function test_admin_gallery_endpoints_require_authentication(): void
    {
        $this->getJson('/api/admin/gallery')->assertStatus(401);
        $this->getJson('/api/admin/gallery/1')->assertStatus(401);
        $this->postJson('/api/admin/gallery', [])->assertStatus(401);
        $this->putJson('/api/admin/gallery/1', [])->assertStatus(401);
        $this->deleteJson('/api/admin/gallery/1')->assertStatus(401);
        $this->getJson('/api/admin/modules/gallery')->assertStatus(401);
    }

    public function test_admin_can_access_module_overview(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->getJson('/api/admin/modules/gallery');

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
                    'albums',
                ],
            ]);

        $this->assertEquals('gallery', $response->json('module'));
        $this->assertEquals('galleryPageLayout', $response->json('data.layout.key'));
    }

    public function test_admin_can_crud_gallery_albums_and_replace_images(): void
    {
        Passport::actingAs($this->admin);

        // 1. Create with an image that has no id (testing auto-id assignment)
        $createPayload = [
            'name' => 'Global Design Systems Expo',
            'category' => 'Design',
            'description' => 'Showcasing modular UI kits, multi-brand themes, and component governance.',
            'coverImage' => 'https://example.com/cover.jpg',
            'images' => [
                [
                    'url' => 'https://example.com/slide1.jpg',
                    'title' => 'Token Architecture',
                    'caption' => 'Design tokens synchronized with frontend codebase',
                ],
            ],
            'order' => 50,
            'isPublished' => true,
        ];

        $createResponse = $this->postJson('/api/admin/gallery', $createPayload);
        $createResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Global Design Systems Expo',
                    'category' => 'Design',
                ],
            ]);

        $id = $createResponse->json('data.id');
        $this->assertNotNull($id);

        $createdImages = $createResponse->json('data.images');
        $this->assertCount(1, $createdImages);
        $this->assertNotNull($createdImages[0]['id'], 'Image should have an auto-assigned id');

        // 2. Read Single
        $showResponse = $this->getJson("/api/admin/gallery/{$id}");
        $showResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'name' => 'Global Design Systems Expo',
                ],
            ]);

        // 3. Update Album and Replace Images
        $updatePayload = [
            'name' => 'Global Design Systems Expo (Updated)',
            'images' => [
                [
                    'id' => $createdImages[0]['id'],
                    'url' => 'https://example.com/slide1-updated.jpg',
                    'title' => 'Token Architecture v2',
                    'caption' => 'Automated Figma tokens sync',
                ],
                [
                    'url' => 'https://example.com/slide2-new.jpg',
                    'title' => 'Accessibility Auditing',
                    'caption' => 'WCAG AAA contrast validation',
                ],
            ],
        ];

        $updateResponse = $this->putJson("/api/admin/gallery/{$id}", $updatePayload);
        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Global Design Systems Expo (Updated)',
                ],
            ]);

        $updatedImages = $updateResponse->json('data.images');
        $this->assertCount(2, $updatedImages);
        $this->assertEquals($createdImages[0]['id'], $updatedImages[0]['id']);
        $this->assertEquals('https://example.com/slide1-updated.jpg', $updatedImages[0]['url']);
        $this->assertNotNull($updatedImages[1]['id']);

        // 4. Delete
        $deleteResponse = $this->deleteJson("/api/admin/gallery/{$id}");
        $deleteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Gallery album deleted successfully.',
            ]);

        // Verify Deletion
        $this->getJson("/api/admin/gallery/{$id}")->assertStatus(404);
    }

    public function test_gallery_validates_required_fields(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->postJson('/api/admin/gallery', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'name',
                ],
            ]);
    }
}
