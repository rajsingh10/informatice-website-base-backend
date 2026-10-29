<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EventApiTest extends TestCase
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

    public function test_public_events_feed_returns_upcoming_events_by_default(): void
    {
        $response = $this->getJson('/api/public/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'total',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'type',
                        'shortDescription',
                        'date',
                        'endDate',
                        'startTime',
                        'endTime',
                        'venue',
                        'isOnline',
                        'price',
                        'image',
                    ],
                ],
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertIsArray($response->json('data'));
    }

    public function test_public_events_feed_supports_when_filter(): void
    {
        // Past events
        $pastRes = $this->getJson('/api/public/events?when=past');
        $pastRes->assertStatus(200);
        $this->assertTrue($pastRes->json('success'));

        // All events
        $allRes = $this->getJson('/api/public/events?when=all');
        $allRes->assertStatus(200);
        $this->assertTrue($allRes->json('success'));
        $this->assertGreaterThanOrEqual($pastRes->json('total'), $allRes->json('total'));
    }

    public function test_public_event_detail_by_slug(): void
    {
        $event = Event::where('status', 'published')->first();
        $this->assertNotNull($event, 'At least one published event should exist');

        $response = $this->getJson("/api/public/events/{$event->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'slug' => $event->slug,
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'type',
                    'shortDescription',
                    'longDescription',
                    'date',
                    'endDate',
                    'startTime',
                    'endTime',
                    'venue',
                    'isOnline',
                    'onlineUrl',
                    'price',
                    'totalSeats',
                    'availableSeats',
                    'agenda',
                    'image',
                    'status',
                    'seo',
                ],
            ]);
    }

    public function test_public_event_detail_returns_404_for_unknown_slug(): void
    {
        $response = $this->getJson('/api/public/events/non-existent-event-slug-xyz');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_admin_events_endpoints_require_authentication(): void
    {
        $this->getJson('/api/admin/events')->assertStatus(401);
        $this->postJson('/api/admin/events', [])->assertStatus(401);
        $this->getJson('/api/admin/modules/events')->assertStatus(401);
        $this->getJson('/api/admin/modules/event-detail')->assertStatus(401);
    }

    public function test_admin_can_access_module_overview(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        // Module feed view
        $feedRes = $this->getJson('/api/admin/modules/events');
        $feedRes->assertStatus(200)
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
                    'events',
                ],
            ]);
        $this->assertEquals('eventsPageLayout', $feedRes->json('data.layout.key'));

        // Module detail view
        $detailRes = $this->getJson('/api/admin/modules/event-detail');
        $detailRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'module',
                'data' => [
                    'layout' => [
                        'key',
                        'templateId',
                        'allowed',
                    ],
                ],
            ]);
        $this->assertEquals('eventDetailPageLayout', $detailRes->json('data.layout.key'));
    }

    public function test_admin_can_crud_events(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        // 1. Create
        $createPayload = [
            'title' => 'Automated Test Hackathon 2027',
            'type' => 'Hackathon',
            'shortDescription' => '48-hour build sprint for AI agent developers.',
            'longDescription' => '<p>Join us to build cutting-edge agentic workflows.</p>',
            'date' => '2027-11-01',
            'endDate' => '2027-11-03',
            'startTime' => '9:00 AM',
            'endTime' => '6:00 PM',
            'venue' => 'Tech Innovation Lab',
            'isOnline' => false,
            'price' => 499,
            'totalSeats' => 200,
            'availableSeats' => 150,
            'agenda' => [
                ['time' => '9:00 AM', 'topic' => 'Opening Ceremony'],
            ],
            'status' => 'published',
        ];

        $createRes = $this->postJson('/api/admin/events', $createPayload);
        $createRes->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $createdId = $createRes->json('data.id');
        $this->assertNotNull($createdId);

        // 2. Read single
        $getRes = $this->getJson("/api/admin/events/{$createdId}");
        $getRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $createdId,
                    'title' => 'Automated Test Hackathon 2027',
                ],
            ]);

        // 3. Update
        $updateRes = $this->putJson("/api/admin/events/{$createdId}", [
            'title' => 'Automated Test Hackathon 2027 (Updated)',
            'availableSeats' => 120,
        ]);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Automated Test Hackathon 2027 (Updated)',
                    'availableSeats' => 120,
                ],
            ]);

        // 4. Delete
        $deleteRes = $this->deleteJson("/api/admin/events/{$createdId}");
        $deleteRes->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNull(Event::find($createdId));
    }

    public function test_event_validates_constraints(): void
    {
        Passport::actingAs($this->admin, ['*'], 'api');

        // Constraint 1: availableSeats must not exceed totalSeats
        $res1 = $this->postJson('/api/admin/events', [
            'title' => 'Invalid Seats Event',
            'date' => '2027-12-01',
            'totalSeats' => 50,
            'availableSeats' => 100,
        ]);
        $res1->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'availableSeats must not exceed totalSeats.',
            ]);

        // Constraint 2: onlineUrl required when isOnline is true
        $res2 = $this->postJson('/api/admin/events', [
            'title' => 'Online Without URL',
            'date' => '2027-12-01',
            'isOnline' => true,
        ]);
        $res2->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'onlineUrl is required when event is online.',
            ]);

        // Constraint 3: endDate must not be before date
        $res3 = $this->postJson('/api/admin/events', [
            'title' => 'Backward Dates Event',
            'date' => '2027-12-10',
            'endDate' => '2027-12-05',
        ]);
        $res3->assertStatus(422);
    }
}
