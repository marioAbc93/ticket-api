<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_can_get_paginated_events(): void
    {
        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'message',
                    'events',
                    'total_pages',
                    'total_events',
                    'current_page',
                 ]);
    }

    public function test_can_get_all_events(): void
    {
        $response = $this->getJson('/api/events/get-all');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'events',
                ]);
    }

    public function test_can_get_event_summary(): void
    {
        $response = $this->getJson('/api/events/summary');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'message',
                    'total_events',
                    'available_events',
                    'soldout_events',
                    'events_with_available_tickets',
                 ]);
    }


    public function test_can_get_event_names_with_available_tickets(): void
    {
        $response = $this->getJson('/api/events-name');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'message',
                    'events',
                 ]);
    }

    public function test_can_get_event_by_id(): void
    {
        $event = Event::first();

        $response = $this->getJson("/api/event/{$event->id}");

        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'evento obtenido correctamente',
                    'event' => ['id' => $event->id],
                 ]);
    }

    public function test_can_delete_event(): void
    {
        $event = Event::first();

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'evento eliminado correctamente',
                 ]);

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}
