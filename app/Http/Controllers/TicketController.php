<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Sale;
use App\Models\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DB;

class TicketController extends Controller
{
    public function index()
    {
        try {

            $tickets = Ticket::with('event')->paginate(10);
            $ticketData = $tickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'full_name' => $ticket->name . ' ' . $ticket->last_name,
                    'customer_mail' => $ticket->customer_mail,
                    'event_id' => $ticket->event_id,
                    'event_name' => $ticket->event->name,
                     'qr_code' => asset('storage/qrcodes/' . basename($ticket->qr_code)),
            ];
            });

            return response()->json([
                'message' => 'Lista de tickets obtenida correctamente',
                'tickets' => $ticketData,
                'total_pages' => $tickets->lastPage(),
                'total_tickets' => $tickets->total(),
                'current_page' => $tickets->currentPage(),
            ], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar tickets',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::with('event')->findOrFail($id);

            $ticketData = [
                'id' => $ticket->id,
                'full_name' => $ticket->name . ' ' . $ticket->last_name,
                'customer_mail' => $ticket->customer_mail,
                'event_id' => $ticket->event_id,
                'event_name' => $ticket->event->name,
                'qr_code' => asset('storage/qrcodes/' . basename($ticket->qr_code)),
                'date' => $ticket->event->date,
            ];

            return response()->json(['message' => 'Ticket obtenido correctamente', 'ticket' => $ticketData], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al recuperar ticket',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }


    public function buyTicket(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'customer_mail' => 'required|email|max:255',
            'payment_method' => 'required|string|max:255',
            'ticket_quantity' => 'required|integer|min:1|max:10',
        ]);

        $ticketQuantity = $validated['ticket_quantity'];

        if ($event->available_tickets < $ticketQuantity) {
            return response()->json([
                'message' => 'La cantidad solicitada de tickets excede los tickets disponibles.',
            ], 400);
        }

        try {
            DB::transaction(function () use ($event, $eventId, $ticketQuantity, $validated) {

                $soldTicketsCount = DB::table('sales')
                                    ->where('event_id', $eventId)
                                    ->lockForUpdate()
                                    ->count();

                if (($soldTicketsCount + $ticketQuantity) > $event->available_tickets) {
                    throw new \Exception('No hay suficientes tickets disponibles para este evento.');
                }

                for ($i = 0; $i < $ticketQuantity; $i++) {
                    $ticketNumber = $soldTicketsCount + $i + 1;
                    $qrCode = QrCode::format('png')->size(200)->generate("http://localhost:5173/evento/{$event->id}/ticket/{$ticketNumber}");

                    $qrCodePath = "qrcodes/{$event->id}_ticket_{$ticketNumber}.png";

                    \Storage::disk('public')->put($qrCodePath, $qrCode);

                    $ticket = Ticket::create([
                        'name' => $validated['name'],
                        'last_name' => $validated['last_name'],
                        'customer_mail' => $validated['customer_mail'],
                        'event_id' => $event->id,
                        'qr_code' => $qrCodePath,
                        'payment_method' => $validated['payment_method'],
                    ]);

                    Sale::create([
                        'ticket_id' => $ticket->id,
                        'event_id' => $event->id,
                    ]);
                }

                $event->available_tickets -= $ticketQuantity;
                $event->save();
            });

            return response()->json(['message' => 'Compra de ticket realizada con éxito'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }




}
