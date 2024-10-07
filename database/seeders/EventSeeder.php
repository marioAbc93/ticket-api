<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $artists = [
            'Maná', 'J Balvin', 'Karol G', 'Shakira',
            'Justin Bieber', 'Bruno Mars'
        ];

        $locations = [
            'Madrid', 'Madrid', 'Málaga', 'Sevilla',
            'Barcelona', 'Barcelona', 'Barcelona', 'Barcelona'
        ];

        $eventData = [];

        for ($i = 0; $i < 15; $i++) {
            $artist = $artists[array_rand($artists)];
            $location = $locations[array_rand($locations)];
            $date = Carbon::now()->addDays(rand(1, 180));
            $ticketValue = rand(50, 200);

            $eventData[] = [
                'name' => $artist . ' en ' . $location,
                'description' => 'Concierto de ' . $artist . ' en ' . $location,
                'date' => $date,
                'ticket_value' => $ticketValue,
                'available_tickets' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Event::insert($eventData);
    }
}
