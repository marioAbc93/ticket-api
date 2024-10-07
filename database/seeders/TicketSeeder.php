<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Sale;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Carlos', 'last_name' => 'Gómez', 'customer_mail' => 'carlos.gomez@example.com'],
            ['name' => 'María', 'last_name' => 'Fernández', 'customer_mail' => 'maria.fernandez@example.com'],
            ['name' => 'José', 'last_name' => 'Martínez', 'customer_mail' => 'jose.martinez@example.com'],
            ['name' => 'Ana', 'last_name' => 'López', 'customer_mail' => 'ana.lopez@example.com'],
            ['name' => 'Juan', 'last_name' => 'Pérez', 'customer_mail' => 'juan.perez@example.com'],
            ['name' => 'Lucía', 'last_name' => 'García', 'customer_mail' => 'lucia.garcia@example.com'],
            ['name' => 'Santiago', 'last_name' => 'Rodríguez', 'customer_mail' => 'santiago.rodriguez@example.com'],
            ['name' => 'Laura', 'last_name' => 'Sánchez', 'customer_mail' => 'laura.sanchez@example.com'],
            ['name' => 'Pedro', 'last_name' => 'Ramírez', 'customer_mail' => 'pedro.ramirez@example.com'],
            ['name' => 'Carmen', 'last_name' => 'Torres', 'customer_mail' => 'carmen.torres@example.com'],
        ];

        $events = Event::all();

        $firstEvent = $events->first();
        if ($firstEvent) {
            for ($i = 0; $i < 10; $i++) {
                $customer = $customers[$i % count($customers)];

                $qrCode = QrCode::format('png')->size(200)->generate("http://localhost:5173/evento/{$firstEvent->id}/ticket/{$i}");

                $qrCodePath = "qrcodes/{$firstEvent->id}_ticket_{$i}.png";
                \Storage::disk('public')->put($qrCodePath, $qrCode);

                $ticket = Ticket::create([
                    'name' => $customer['name'],
                    'last_name' => $customer['last_name'],
                    'customer_mail' => $customer['customer_mail'],
                    'event_id' => $firstEvent->id,
                    'qr_code' => $qrCodePath,
                ]);

                // Registrar una venta
                Sale::create([
                    'ticket_id' => $ticket->id,
                    'event_id' => $firstEvent->id,
                ]);
            }
        }

        foreach ($events->skip(1) as $event) {
            for ($i = 0; $i < 3; $i++) {
                $customer = $customers[$i % count($customers)];

                $qrCode = QrCode::format('png')->size(200)->generate("http://localhost:5173/evento/{$event->id}/ticket/{$i}");

                $qrCodePath = "qrcodes/{$event->id}_ticket_{$i}.png";
                \Storage::disk('public')->put($qrCodePath, $qrCode);

                $ticket = Ticket::create([
                    'name' => $customer['name'],
                    'last_name' => $customer['last_name'],
                    'customer_mail' => $customer['customer_mail'],
                    'event_id' => $event->id,
                    'qr_code' => $qrCodePath,
                ]);

                Sale::create([
                    'ticket_id' => $ticket->id,
                    'event_id' => $event->id,
                ]);
            }
        }
    }
}
