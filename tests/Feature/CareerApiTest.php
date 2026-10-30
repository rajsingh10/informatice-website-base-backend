<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Career;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CareerApiTest extends TestCase
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

    public function test_public_careers_feed_returns_open_positions_only(): void
    {
        $openCareer = Career::create([
            'title' => 'Open Cloud Architect Position',
            'department' => 'Cloud',
            'location' => 'Remote',
            'type' => 'full-time',
            'experience' => '3+ years',
            'description' => '<p>Leading multi-cloud setups.</p>',
            'requirements' => ['AWS', 'Kubernetes'],
            'applyUrl' => 'mailto:jobs@example.com',
            'status' => 'open',
            'order' => 1,
            'postedAt' => now(),
        ]);

        $closedCareer = Career::create([
            'title' => 'Closed Legacy DevOps Position',
            'department' => 'Cloud',
            'location' => 'Remote',
            'type' => 'full-time',
            'status' => 'closed',
            'order' => 2,
            'postedAt' => now(),
        ]);

        $response = $this->getJson('/api/public/careers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'department',
                        'location',
                        'type',
                        'experience',
                        'description',
                        'requirements',
                        'applyUrl',
                        'status',
                        'order',
                        'postedAt',
                    ],
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $data = $response->json('data');
        $slugs = collect($data)->pluck('slug');
        $this->assertTrue($slugs->contains($openCareer->slug));
        $this->assertFalse($slugs->contains($closedCareer->slug));

        // Clean up
        $openCareer->delete();
        $closedCareer->delete();
    }

    public function test_public_career_detail_by_slug(): void
    {
        $career = Career::create([
            'title' => 'Senior Frontend Architect',
            'department' => 'Engineering',
            'location' => 'Remote',
            'type' => 'full-time',
            'description' => '<p>Lead React micro-frontends.</p>',
            'status' => 'open',
            'order' => 1,
            'postedAt' => now(),
        ]);

        $response = $this->getJson("/api/public/careers/{$career->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $career->id,
                    'title' => 'Senior Frontend Architect',
                    'slug' => $career->slug,
                    'status' => 'open',
                ],
            ]);

        // Non-existent slug returns 404
        $notFound = $this->getJson('/api/public/careers/non-existent-career-slug-xyz');
        $notFound->assertStatus(404);

        $career->delete();
    }

    public function test_admin_career_module_overview(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $response = $this->getJson('/api/admin/modules/career');

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
                    'openings',
                ],
            ]);

        $this->assertEquals('career', $response->json('module'));
        $this->assertEquals('careerPageLayout', $response->json('data.layout.key'));
    }

    public function test_admin_careers_crud_lifecycle(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        // 1. Create
        $createRes = $this->postJson('/api/admin/careers', [
            'title' => 'Lead Data Scientist',
            'department' => 'AI & Data',
            'location' => 'London',
            'type' => 'contract',
            'experience' => '4-6 years',
            'description' => '<p>Build LLM evaluation pipelines.</p>',
            'requirements' => ['Python', 'PyTorch', 'Vector DBs'],
            'applyUrl' => 'mailto:ai-jobs@example.com',
            'status' => 'open',
            'order' => 10,
        ]);

        $createRes->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Lead Data Scientist',
                    'department' => 'AI & Data',
                    'type' => 'contract',
                ],
            ]);

        $id = $createRes->json('data.id');
        $this->assertNotNull($id);

        // 2. Read single
        $showRes = $this->getJson("/api/admin/careers/{$id}");
        $showRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'title' => 'Lead Data Scientist',
                ],
            ]);

        // 3. Update
        $updateRes = $this->putJson("/api/admin/careers/{$id}", [
            'title' => 'Principal Data Scientist & AI Lead',
            'status' => 'closed',
        ]);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'title' => 'Principal Data Scientist & AI Lead',
                    'status' => 'closed',
                ],
            ]);

        // 4. Delete
        $delRes = $this->deleteJson("/api/admin/careers/{$id}");
        $delRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // 5. Verify deleted
        $verifyRes = $this->getJson("/api/admin/careers/{$id}");
        $verifyRes->assertStatus(404);
    }

    public function test_career_admin_routes_require_authentication(): void
    {
        $this->postJson('/api/admin/careers', ['title' => 'Unauthorized Position'])
            ->assertStatus(401);

        $this->deleteJson('/api/admin/careers/99999')
            ->assertStatus(401);
    }

    public function test_direct_aliases_without_api_prefix_work(): void
    {
        $career = Career::create([
            'title' => 'Direct Alias Engineer',
            'department' => 'Core',
            'status' => 'open',
            'order' => 1,
            'postedAt' => now(),
        ]);

        $res1 = $this->getJson('/careers');
        $res1->assertStatus(200)->assertJson(['success' => true]);

        $res2 = $this->getJson('/public/careers');
        $res2->assertStatus(200)->assertJson(['success' => true]);

        $res3 = $this->getJson("/careers/{$career->slug}");
        $res3->assertStatus(200)->assertJson(['success' => true]);

        $career->delete();
    }
}
