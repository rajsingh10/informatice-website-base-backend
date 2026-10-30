<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Faq;
use Laravel\Passport\Passport;
use Tests\TestCase;

class FaqApiTest extends TestCase
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

    public function test_public_faqs_returns_published_items_only(): void
    {
        $pubFaq = Faq::create([
            'question' => 'How do we schedule automated backups?',
            'answer' => '<p>Backups run daily at 02:00 UTC.</p>',
            'category' => 'Infrastructure',
            'order' => 1,
            'isPublished' => true,
        ]);

        $draftFaq = Faq::create([
            'question' => 'Unpublished internal question?',
            'answer' => '<p>Internal draft.</p>',
            'category' => 'Internal',
            'order' => 2,
            'isPublished' => false,
        ]);

        $response = $this->getJson('/api/public/faqs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'question',
                        'answer',
                        'category',
                        'order',
                    ],
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($pubFaq->id));
        $this->assertFalse($ids->contains($draftFaq->id));

        $pubFaq->delete();
        $draftFaq->delete();
    }

    public function test_public_faqs_filter_by_category_and_search(): void
    {
        $faq = Faq::create([
            'question' => 'What is your kubernetes disaster recovery process?',
            'answer' => '<p>Multi-region replica failover in 60 seconds.</p>',
            'category' => 'Disaster Recovery',
            'order' => 1,
            'isPublished' => true,
        ]);

        // Category filter
        $res1 = $this->getJson('/api/public/faqs?category=Disaster%20Recovery');
        $res1->assertStatus(200);
        $this->assertTrue(collect($res1->json('data'))->pluck('id')->contains($faq->id));

        // Keyword search
        $res2 = $this->getJson('/api/public/faqs?q=disaster');
        $res2->assertStatus(200);
        $this->assertTrue(collect($res2->json('data'))->pluck('id')->contains($faq->id));

        $faq->delete();
    }

    public function test_admin_faq_module_overview(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $response = $this->getJson('/api/admin/modules/faq');

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
                    'categories',
                    'faqs',
                ],
            ]);

        $this->assertEquals('faq', $response->json('module'));
        $this->assertEquals('faqPageLayout', $response->json('data.layout.key'));
    }

    public function test_admin_bulk_save_faqs_replaces_and_preserves_order(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        $initial = Faq::create([
            'question' => 'Initial Question To Be Replaced',
            'answer' => '<p>Will be updated</p>',
            'category' => 'Testing',
            'order' => 1,
            'isPublished' => true,
        ]);

        $bulkPayload = [
            'faqs' => [
                [
                    'id' => $initial->id,
                    'question' => 'Updated Bulk Question 1',
                    'answer' => '<p>Answer updated in bulk</p>',
                    'category' => 'General',
                    'order' => 1,
                    'isPublished' => true,
                ],
                [
                    'question' => 'Brand New Bulk Question 2',
                    'answer' => '<p>Created via bulk replace</p>',
                    'category' => 'General',
                    'order' => 2,
                    'isPublished' => true,
                ],
            ],
        ];

        $response = $this->putJson('/api/admin/faqs', $bulkPayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'All FAQs updated successfully.',
            ]);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals('Updated Bulk Question 1', $data[0]['question']);
        $this->assertEquals('Brand New Bulk Question 2', $data[1]['question']);
    }

    public function test_admin_single_faq_crud_lifecycle(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        // 1. Create single item
        $createRes = $this->postJson('/api/admin/faqs/item', [
            'question' => 'Can we customize SLA terms?',
            'answer' => '<p>Yes, enterprise plans include customizable SLAs up to 99.99%.</p>',
            'category' => 'Contracts',
            'order' => 15,
            'isPublished' => true,
        ]);

        $createRes->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'question' => 'Can we customize SLA terms?',
                    'category' => 'Contracts',
                ],
            ]);

        $id = $createRes->json('data.id');
        $this->assertNotNull($id);

        // 2. Fetch single
        $getRes = $this->getJson("/api/admin/faqs/{$id}");
        $getRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $id],
            ]);

        // 3. Update single
        $updateRes = $this->putJson("/api/admin/faqs/{$id}", [
            'question' => 'Can we customize SLA response times and uptime?',
            'isPublished' => false,
        ]);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $id,
                    'question' => 'Can we customize SLA response times and uptime?',
                    'isPublished' => false,
                ],
            ]);

        // 4. Delete single
        $delRes = $this->deleteJson("/api/admin/faqs/{$id}");
        $delRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // 5. Verify deleted
        $verifyRes = $this->getJson("/api/admin/faqs/{$id}");
        $verifyRes->assertStatus(404);
    }

    public function test_faq_admin_routes_require_authentication(): void
    {
        $this->postJson('/api/admin/faqs', ['question' => 'Unauthorized'])
            ->assertStatus(401);

        $this->deleteJson('/api/admin/faqs/99999')
            ->assertStatus(401);
    }

    public function test_direct_faq_aliases_without_api_prefix_work(): void
    {
        $faq = Faq::create([
            'question' => 'Alias FAQ Test',
            'answer' => '<p>Direct web route alias test.</p>',
            'category' => 'Direct',
            'order' => 1,
            'isPublished' => true,
        ]);

        $res1 = $this->getJson('/faqs');
        $res1->assertStatus(200)->assertJson(['success' => true]);

        $res2 = $this->getJson('/public/faqs');
        $res2->assertStatus(200)->assertJson(['success' => true]);

        $faq->delete();
    }
}

