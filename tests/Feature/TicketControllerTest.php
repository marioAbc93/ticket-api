<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    public function test_can_get_paginated_tickets(): void
    {
        $response = $this->getJson('/api/tickets');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'message',
                    'tickets',
                    'total_pages',
                    'total_tickets',
                    'current_page',
                 ]);
    }

    public function test_can_get_ticket_by_id(): void
    {
        $ticket = Ticket::first();

        $this->assertNotNull($ticket);

        $response = $this->getJson("/api/ticket/{$ticket->id}");

        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'Ticket obtenido correctamente',
                    'ticket' => ['id' => $ticket->id],
                 ]);
    }

    public function test_can_buy_tickets_for_event(): void
    {
        $event = Event::skip(3)->first();

        $data = [
            'name' => 'Mario',
            'last_name' => 'Barros',
            'customer_mail' => 'mario.barros@ticketapp.com',
            'payment_method' => 'credit_card',
            'ticket_quantity' => 1,
        ];

        $response = $this->postJson("/api/events/{$event->id}/buy", $data);
        //dd($response->json());
        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'Compra de ticket realizada con éxito',
                 ]);

        $this->assertDatabaseHas('tickets', [
            'name' => 'Mario',
            'last_name' => 'Barros',
            'customer_mail' => 'mario.barros@ticketapp.com',
            'event_id' => $event->id,
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'available_tickets' => $event->available_tickets - 1,
        ]);

    }




    public function test_cannot_buy_more_tickets_than_available(): void
    {
        $event = Event::first();

        $data = [
            'name' => 'Mario',
            'last_name' => 'Barros',
            'customer_mail' => 'mario.barros@ticketapp.com',
            'payment_method' => 'paypal',
            'ticket_quantity' => $event->available_tickets + 1,
        ];

        $response = $this->postJson("/api/events/{$event->id}/buy", $data);

        $response->assertStatus(400)
                 ->assertJson([
                    'message' => 'La cantidad solicitada de tickets excede los tickets disponibles.',
                 ]);
    }


}
