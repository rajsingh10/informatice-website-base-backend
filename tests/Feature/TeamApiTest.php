<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\TeamMember;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TeamApiTest extends TestCase
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

    public function test_public_team_feed_returns_published_members(): void
    {
        $response = $this->getJson('/api/public/team');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'designation',
                        'department',
                        'photo',
                        'bio',
                        'social' => [
                            'linkedin',
                            'twitter',
                        ],
                        'isLeadership',
                        'order',
                        'isPublished',
                    ],
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertIsArray($response->json('data'));
    }

    public function test_public_team_feed_supports_department_and_leadership_filters(): void
    {
        $deptResponse = $this->getJson('/api/public/team?department=Engineering');
        $deptResponse->assertStatus(200);

        foreach ($deptResponse->json('data') as $member) {
            $this->assertEquals('Engineering', $member['department']);
        }

        $leadershipResponse = $this->getJson('/api/public/team?isLeadership=1');
        $leadershipResponse->assertStatus(200);

        foreach ($leadershipResponse->json('data') as $member) {
            $this->assertTrue($member['isLeadership']);
        }
    }

    public function test_public_team_member_detail_by_id(): void
    {
        $member = TeamMember::published()->first();
        $this->assertNotNull($member, 'Seeded team member expected for test.');

        $response = $this->getJson("/api/public/team/{$member->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $member->id,
                    'name' => $member->name,
                    'designation' => $member->designation,
                ],
            ]);
    }

    public function test_public_team_returns_404_for_unknown_member(): void
    {
        $response = $this->getJson('/api/public/team/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Team member not found.',
            ]);
    }

    public function test_admin_team_endpoints_require_authentication(): void
    {
        $this->getJson('/api/admin/team')->assertStatus(401);
        $this->getJson('/api/admin/team/1')->assertStatus(401);
        $this->postJson('/api/admin/team', [])->assertStatus(401);
        $this->putJson('/api/admin/team/1', [])->assertStatus(401);
        $this->deleteJson('/api/admin/team/1')->assertStatus(401);
        $this->getJson('/api/admin/modules/team')->assertStatus(401);
    }

    public function test_admin_can_access_module_overview(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->getJson('/api/admin/modules/team');

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
                    'members',
                ],
            ]);

        $this->assertEquals('team', $response->json('module'));
        $this->assertEquals('teamPageLayout', $response->json('data.layout.key'));
    }

    public function test_admin_can_crud_team_members(): void
    {
        Passport::actingAs($this->admin);

        // 1. Create
        $createPayload = [
            'name' => 'Test Team Engineer',
            'designation' => 'Senior Quality Automation Engineer',
            'department' => 'Engineering',
            'photo' => 'https://example.com/photo.jpg',
            'bio' => 'Passionate about robust backend APIs and automated tests.',
            'social' => [
                'linkedin' => 'https://linkedin.com/in/testengineer',
                'twitter' => 'https://x.com/testengineer',
                'github' => 'https://github.com/testengineer',
            ],
            'isLeadership' => false,
            'order' => 50,
            'isPublished' => true,
        ];

        $createResponse = $this->postJson('/api/admin/team', $createPayload);
        $createResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Test Team Engineer',
                    'designation' => 'Senior Quality Automation Engineer',
                    'department' => 'Engineering',
                    'isLeadership' => false,
                ],
            ]);

        $id = $createResponse->json('data.id');
        $this->assertNotNull($id);

        // 2. Read Single
        $showResponse = $this->getJson("/api/admin/team/{$id}");
        $showResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'name' => 'Test Team Engineer',
                ],
            ]);

        // 3. Update
        $updateResponse = $this->putJson("/api/admin/team/{$id}", [
            'designation' => 'Staff QA Automation Engineer',
            'isLeadership' => true,
        ]);
        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'designation' => 'Staff QA Automation Engineer',
                    'isLeadership' => true,
                ],
            ]);

        // 4. Delete
        $deleteResponse = $this->deleteJson("/api/admin/team/{$id}");
        $deleteResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Team member deleted successfully.',
            ]);

        // Verify Deletion
        $this->getJson("/api/admin/team/{$id}")->assertStatus(404);
    }

    public function test_team_member_validates_required_fields(): void
    {
        Passport::actingAs($this->admin);

        $response = $this->postJson('/api/admin/team', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'name',
                    'designation',
                ],
            ]);
    }
}
