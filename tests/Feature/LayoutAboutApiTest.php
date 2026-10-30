<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Layout;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LayoutAboutApiTest extends TestCase
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

    public function test_public_layouts_includes_about_page_layout(): void
    {
        $response = $this->getJson('/api/public/layouts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'aboutPageLayout',
                ],
            ]);
    }

    public function test_admin_can_update_about_page_layout(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->putJson('/api/admin/layouts/aboutPageLayout', [
            'layout' => 'layout2',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'layout' => 'layout2',
                ],
            ]);

        $this->assertEquals('layout2', Layout::find('aboutPageLayout')?->templateId);
    }

    public function test_admin_receives_422_for_invalid_about_template(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->putJson('/api/admin/layouts/aboutPageLayout', [
            'layout' => 'layout99',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_admin_can_access_about_module_overview(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->getJson('/api/admin/modules/about');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'module' => 'about',
                'data' => [
                    'layout' => [
                        'key' => 'aboutPageLayout',
                    ],
                ],
            ]);
    }
}

