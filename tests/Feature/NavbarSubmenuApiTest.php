<?php

namespace Tests\Feature;

use App\Models\Admin;
use Laravel\Passport\Passport;
use Tests\TestCase;

class NavbarSubmenuApiTest extends TestCase
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

    public function test_admin_bulk_replace_and_public_filtering_of_multi_tier_submenus(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $multiTierMenu = [
            [
                'id' => 'nav-home',
                'label' => 'Home',
                'url' => '/',
                'enabled' => true,
            ],
            [
                'id' => 'nav-services',
                'label' => 'Services',
                'url' => '/services',
                'enabled' => true,
                'submenu' => [
                    [
                        'id' => 'nav-cloud',
                        'label' => 'Cloud Solutions',
                        'url' => '/services/cloud',
                        'enabled' => true,
                        'submenu' => [
                            [
                                'id' => 'nav-aws',
                                'label' => 'AWS Architecture',
                                'url' => '/services/cloud/aws',
                                'enabled' => true,
                            ],
                            [
                                'id' => 'nav-azure',
                                'label' => 'Azure Enterprise',
                                'url' => '/services/cloud/azure',
                                'enabled' => false, // disabled nested item
                            ],
                        ],
                    ],
                    [
                        'id' => 'nav-ai',
                        'label' => 'Generative AI',
                        'url' => '/services/ai',
                        'enabled' => false, // disabled top-level submenu item
                        'submenu' => [
                            [
                                'id' => 'nav-llm',
                                'label' => 'LLM Fine-tuning',
                                'url' => '/services/ai/llm',
                                'enabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 1. Bulk update full hierarchy
        $updateRes = $this->putJson('/api/admin/navigation/navbar/items', ['items' => $multiTierMenu]);
        $updateRes->assertStatus(200)->assertJson(['success' => true]);

        // 2. Admin GET returns all levels and disabled items
        $adminRes = $this->getJson('/api/admin/navigation/navbar');
        $adminRes->assertStatus(200);
        $adminItems = $adminRes->json('data.items');

        $servicesItem = collect($adminItems)->firstWhere('id', 'nav-services');
        $this->assertNotNull($servicesItem);
        $this->assertCount(2, $servicesItem['submenu']);
        $this->assertEquals('nav-cloud', $servicesItem['submenu'][0]['id']);
        $this->assertEquals('nav-ai', $servicesItem['submenu'][1]['id']);
        $this->assertCount(2, $servicesItem['submenu'][0]['submenu']); // Layer 3 in admin

        // 3. Public GET returns only enabled items recursively
        $publicRes = $this->getJson('/api/public/navigation/navbar');
        $publicRes->assertStatus(200);
        $publicItems = $publicRes->json('data.items');

        $publicServices = collect($publicItems)->firstWhere('id', 'nav-services');
        $this->assertNotNull($publicServices);
        // nav-ai is disabled so only nav-cloud remains
        $this->assertCount(1, $publicServices['submenu']);
        $this->assertEquals('nav-cloud', $publicServices['submenu'][0]['id']);

        // In nav-cloud, nav-azure is disabled so only nav-aws remains
        $this->assertCount(1, $publicServices['submenu'][0]['submenu']);
        $this->assertEquals('nav-aws', $publicServices['submenu'][0]['submenu'][0]['id']);

        // Reset to default items
        $this->postJson('/api/admin/navigation/navbar/reset');
    }

    public function test_admin_can_add_subitem_and_sub_subitem_by_parent_id(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        // Reset first to know state
        $this->postJson('/api/admin/navigation/navbar/reset');

        // 1. Add sub-item under nav-3 (Services)
        $subRes = $this->postJson('/api/admin/navigation/navbar/items', [
            'parentId' => 'nav-3',
            'label' => 'Microservices Architecture',
            'url' => '/services/microservices',
            'enabled' => true,
        ]);

        $subRes->assertStatus(201)
            ->assertJson([
                'success' => true,
                'parentId' => 'nav-3',
                'item' => [
                    'label' => 'Microservices Architecture',
                    'url' => '/services/microservices',
                ],
            ]);

        $childId = $subRes->json('item.id');
        $this->assertNotNull($childId);

        // 2. Add sub-subitem under the newly created sub-item (Layer 3)
        $subSubRes = $this->postJson("/api/admin/navigation/navbar/items/{$childId}/subitems", [
            'label' => 'Kubernetes Mesh',
            'url' => '/services/microservices/k8s',
            'enabled' => true,
        ]);

        $subSubRes->assertStatus(201);
        $leafId = $subSubRes->json('item.id');

        // 3. Verify hierarchy via Admin GET
        $adminRes = $this->getJson('/api/admin/navigation/navbar');
        $services = collect($adminRes->json('data.items'))->firstWhere('id', 'nav-3');
        $this->assertNotEmpty($services['submenu']);
        $child = collect($services['submenu'])->firstWhere('id', $childId);
        $this->assertNotNull($child);
        $leaf = collect($child['submenu'])->firstWhere('id', $leafId);
        $this->assertNotNull($leaf);
        $this->assertEquals('Kubernetes Mesh', $leaf['label']);

        // 4. Update the deep leaf item
        $updRes = $this->putJson("/api/admin/navigation/navbar/items/{$leafId}", [
            'label' => 'Enterprise Istio Service Mesh',
        ]);
        $updRes->assertStatus(200)->assertJson(['success' => true]);

        // 5. Toggle the deep leaf item
        $togRes = $this->postJson("/api/admin/navigation/navbar/items/{$leafId}/toggle");
        $togRes->assertStatus(200);
        $this->assertFalse($togRes->json('item.enabled'));

        // 6. Delete the leaf item
        $delRes = $this->deleteJson("/api/admin/navigation/navbar/items/{$leafId}");
        $delRes->assertStatus(200)->assertJson(['success' => true]);

        // Reset back to defaults
        $this->postJson('/api/admin/navigation/navbar/reset');
    }

    public function test_add_subitem_with_invalid_parent_id_returns_404(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $res = $this->postJson('/api/admin/navigation/navbar/items', [
            'parentId' => 'non-existent-nav-parent-9999',
            'label' => 'Orphan Item',
            'url' => '/orphan',
        ]);

        $res->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => "Parent menu item with ID 'non-existent-nav-parent-9999' not found.",
            ]);
    }
}
