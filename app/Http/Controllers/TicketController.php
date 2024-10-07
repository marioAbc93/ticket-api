<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Sale;
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
                    'qr_code' => $ticket->qr_code,
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
            $ticket = Ticket::findOrFail($id);
            return response()->json(['message' => 'ticket obtenido correctamente', 'ticket' => $ticket], 200);
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
        ]);

        $maxTickets = 10;

        try {
            DB::transaction(function () use ($event, $eventId, $maxTickets, $validated) {
                $soldTicketsCount = DB::table('sales')
                                    ->where('event_id', $eventId)
                                    ->lockForUpdate()
                                    ->count();

                if ($soldTicketsCount >= $maxTickets) {
                    throw new \Exception('No hay más tickets disponibles para este evento.');
                }

                $ticketNumber = $soldTicketsCount + 1;
                $qrCode = QrCode::format('png')->size(200)->generate("http://localhost:5173/evento/{$event->id}/ticket/{$ticketNumber}");

               $qrCodePath = "qrcodes/{$event->id}_ticket_{$ticketNumber}.png";

                \Storage::disk('public')->put($qrCodePath, $qrCode);


                $ticket = Ticket::create([
                    'name' => $validated['name'],
                    'last_name' => $validated['last_name'],
                    'customer_mail' => $validated['customer_mail'],
                    'event_id' => $event->id,
                    'qr_code' => $qrCodePath,
                ]);

                Sale::create([
                    'ticket_id' => $ticket->id,
                    'event_id' => $event->id,
                ]);
            });

            return response()->json(['message' => 'Compra de ticket realizada con éxito'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return response()->json(['message' => 'ticket eliminado correctamente'], 200);
        } catch (Exception $exc) {
            return response()->json([
                'message' => 'Error al eliminar ticket',
                'error' => $exc->getMessage(),
            ], 500);
        }
    }

}
