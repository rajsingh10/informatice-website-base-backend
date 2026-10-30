<?php

namespace Tests\Feature;

use App\Models\Admin;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LegalApiTest extends TestCase
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

    public function test_public_legal_terms_and_privacy_feed(): void
    {
        // 1. Terms
        $termsRes = $this->getJson('/api/public/legal/terms');
        $termsRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'type',
                    'title',
                    'content',
                    'lastUpdated',
                ],
            ]);
        $this->assertEquals('terms', $termsRes->json('data.type'));

        // 2. Privacy
        $privacyRes = $this->getJson('/api/public/legal/privacy');
        $privacyRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'type',
                    'title',
                    'content',
                    'lastUpdated',
                ],
            ]);
        $this->assertEquals('privacy', $privacyRes->json('data.type'));

        // 3. Invalid type returns 404
        $invalidRes = $this->getJson('/api/public/legal/invalid-page-type');
        $invalidRes->assertStatus(404);
    }

    public function test_public_legal_aliases_work_with_and_without_api_prefix(): void
    {
        // /api/public/terms & /api/public/privacy
        $this->getJson('/api/public/terms')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/api/public/privacy')->assertStatus(200)->assertJson(['success' => true]);

        // Direct web aliases: /terms, /privacy, /public/terms, /public/privacy
        $this->getJson('/terms')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/privacy')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/public/terms')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/public/privacy')->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_legal_module_overview(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        // Module 15: Terms overview
        $termsMod = $this->getJson('/api/admin/modules/terms');
        $termsMod->assertStatus(200)
            ->assertJson([
                'success' => true,
                'module' => 'terms',
                'data' => [
                    'layout' => [
                        'key' => 'termsPageLayout',
                    ],
                ],
            ]);

        // Module 16: Privacy overview
        $privacyMod = $this->getJson('/api/admin/modules/privacy');
        $privacyMod->assertStatus(200)
            ->assertJson([
                'success' => true,
                'module' => 'privacy',
                'data' => [
                    'layout' => [
                        'key' => 'privacyPageLayout',
                    ],
                ],
            ]);
    }

    public function test_admin_update_terms_content(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $updateRes = $this->putJson('/api/admin/legal/terms', [
            'title' => 'Master Terms of Service 2026',
            'content' => '<h2>1. Updated Terms</h2><p>Enterprise terms revised.</p>',
            'lastUpdated' => 'November 2026',
        ]);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'type' => 'terms',
                    'title' => 'Master Terms of Service 2026',
                    'lastUpdated' => 'November 2026',
                ],
            ]);

        // Verify public reflects the update
        $pubRes = $this->getJson('/api/public/legal/terms');
        $pubRes->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Master Terms of Service 2026',
                    'lastUpdated' => 'November 2026',
                ],
            ]);
    }

    public function test_admin_update_privacy_content(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $updateRes = $this->putJson('/api/admin/legal/privacy', [
            'title' => 'Global Privacy & Data Protection Policy',
            'content' => '<h2>1. GDPR & CCPA Compliance</h2><p>Full data governance standards enforced.</p>',
            'lastUpdated' => 'November 2026',
        ]);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'type' => 'privacy',
                    'title' => 'Global Privacy & Data Protection Policy',
                    'lastUpdated' => 'November 2026',
                ],
            ]);

        // Verify public reflects the update
        $pubRes = $this->getJson('/api/public/legal/privacy');
        $pubRes->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Global Privacy & Data Protection Policy',
                    'lastUpdated' => 'November 2026',
                ],
            ]);
    }

    public function test_admin_legal_aliases_without_legal_path_segment(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $this->getJson('/api/admin/terms')->assertStatus(200)->assertJson(['success' => true]);
        $this->getJson('/api/admin/privacy')->assertStatus(200)->assertJson(['success' => true]);

        $this->putJson('/api/admin/terms', ['title' => 'Terms Alias Updated'])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->putJson('/api/admin/privacy', ['title' => 'Privacy Alias Updated'])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_legal_admin_routes_require_authentication(): void
    {
        $this->putJson('/api/admin/legal/terms', ['title' => 'Unauthorized'])
            ->assertStatus(401);

        $this->putJson('/api/admin/legal/privacy', ['title' => 'Unauthorized'])
            ->assertStatus(401);
    }
}
